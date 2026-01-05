#!/usr/bin/env python3
"""
Build `resources/js/data/italy_places.json` (province + comuni per provincia) from ISTAT XLSX.

Source page: https://www.istat.it/classificazione/codici-dei-comuni-delle-province-e-delle-regioni/
Direct XLSX: https://www.istat.it/wp-content/uploads/2024/09/Elenco-comuni-italiani.xlsx
"""

from __future__ import annotations

import io
import json
import re
import urllib.request
import xml.etree.ElementTree as ET
import zipfile
from collections import defaultdict
from pathlib import Path


XLSX_URL = "https://www.istat.it/wp-content/uploads/2024/09/Elenco-comuni-italiani.xlsx"
OUTPUT_PATH = Path("resources/js/data/italy_places.json")

# Columns (0-based) in ISTAT sheet:
# 6  -> Denominazione in italiano (Comune)
# 11 -> Denominazione dell'Unità territoriale sovracomunale (Provincia/Città metropolitana)
# 14 -> Sigla automobilistica (Provincia)
COL_CITY = 6
COL_PROVINCE_NAME = 11
COL_PROVINCE_CODE = 14


def _col_letters_to_idx(letters: str) -> int:
    idx = 0
    for ch in letters:
        idx = idx * 26 + (ord(ch) - 64)
    return idx - 1


def _cell_value(c: ET.Element, ns: dict[str, str], shared: list[str]) -> str | None:
    t = c.attrib.get("t")
    v = c.find("m:v", ns)
    if v is None or v.text is None:
        return None
    val = v.text
    if t == "s":
        try:
            return shared[int(val)]
        except Exception:
            return None
    return val


def main() -> None:
    raw = urllib.request.urlopen(XLSX_URL, timeout=60).read()
    z = zipfile.ZipFile(io.BytesIO(raw))

    ns = {"m": "http://schemas.openxmlformats.org/spreadsheetml/2006/main"}

    # Resolve first worksheet path
    wb = ET.fromstring(z.read("xl/workbook.xml"))
    sheets = wb.find("m:sheets", ns)
    first_sheet = sheets.find("m:sheet", ns) if sheets is not None else None
    if first_sheet is None:
        raise SystemExit("Cannot find first worksheet")
    rid = first_sheet.attrib.get(
        "{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id"
    )
    rels = ET.fromstring(z.read("xl/_rels/workbook.xml.rels"))
    rns = {"r": "http://schemas.openxmlformats.org/package/2006/relationships"}
    target = None
    for rel in rels.findall("r:Relationship", rns):
        if rel.attrib.get("Id") == rid:
            target = rel.attrib.get("Target")
            break
    if not target:
        raise SystemExit("Cannot resolve worksheet relationship target")
    ws_path = "xl/" + target.lstrip("/")
    ws = ET.fromstring(z.read(ws_path))

    # Shared strings
    shared: list[str] = []
    if "xl/sharedStrings.xml" in z.namelist():
        sst = ET.fromstring(z.read("xl/sharedStrings.xml"))
        for si in sst.findall("m:si", ns):
            txt = "".join(t.text or "" for t in si.findall(".//m:t", ns))
            shared.append(txt)

    col_re = re.compile(r"([A-Z]+)")

    sheet_data = ws.find("m:sheetData", ns)
    rows = sheet_data.findall("m:row", ns) if sheet_data is not None else []

    cities_by_prov: dict[str, set[str]] = defaultdict(set)
    prov_name: dict[str, str] = {}

    for row in rows[1:]:
        values: dict[int, str | None] = {}
        for c in row.findall("m:c", ns):
            r = c.attrib.get("r")
            if not r:
                continue
            col = col_re.match(r).group(1)
            idx = _col_letters_to_idx(col)
            values[idx] = _cell_value(c, ns, shared)

        city = (values.get(COL_CITY) or "").strip()
        prov_code = (values.get(COL_PROVINCE_CODE) or "").strip()
        prov_nm = (values.get(COL_PROVINCE_NAME) or "").strip()

        if not city or not prov_code:
            continue

        cities_by_prov[prov_code].add(city)
        if prov_nm:
            prov_name[prov_code] = prov_nm

    out = {"provinces": [], "citiesByProvince": {}}
    for code in sorted(cities_by_prov):
        out["provinces"].append({"code": code, "name": prov_name.get(code, "")})
        out["citiesByProvince"][code] = sorted(cities_by_prov[code])

    OUTPUT_PATH.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT_PATH.write_text(json.dumps(out, ensure_ascii=False), encoding="utf-8")
    print(f"Wrote {OUTPUT_PATH} (provinces={len(out['provinces'])}, cities={sum(len(v) for v in out['citiesByProvince'].values())})")


if __name__ == "__main__":
    main()


