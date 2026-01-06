<script>
    /* eslint-disable */
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import { router } from "@inertiajs/svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Badge } from "@/lib/components/ui/badge";
    import { Button } from "@/lib/components/ui/button";
    import { ChevronLeft, ChevronRight } from "lucide-svelte";

    let { events, currentDate } = $props();

    let current = $derived(new Date(currentDate));
    let monthLabel = $derived.by(() =>
        current.toLocaleString("it-IT", { month: "long", year: "numeric" }),
    );

    function pad2(n) {
        return String(n).padStart(2, "0");
    }

    function ymd(d) {
        return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
    }

    // Monday-first index: 0=Mon ... 6=Sun
    function weekdayIndex(d) {
        return (d.getDay() + 6) % 7;
    }

    function visit(date) {
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        router.get("/me/events", { year, month }, { preserveState: false });
    }

    function previousMonth() {
        const d = new Date(current);
        d.setMonth(d.getMonth() - 1);
        visit(d);
    }

    function nextMonth() {
        const d = new Date(current);
        d.setMonth(d.getMonth() + 1);
        visit(d);
    }

    function fmtDateTime(iso) {
        if (!iso) return "";
        const d = new Date(iso);
        if (Number.isNaN(d.getTime())) return "";
        return d.toLocaleString("it-IT", {
            weekday: "short",
            day: "2-digit",
            month: "short",
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    function typeLabel(t) {
        if (t === "fair") return "Fiera";
        if (t === "festival") return "Sagra";
        if (t === "meeting") return "Riunione";
        return t || "-";
    }

    let eventsByDay = $derived.by(() => {
        const map = new Map();
        for (const e of events || []) {
            const d = e?.start_date ? new Date(e.start_date) : null;
            if (!d || Number.isNaN(d.getTime())) continue;
            const key = ymd(d);
            const arr = map.get(key) || [];
            arr.push(e);
            map.set(key, arr);
        }
        return map;
    });

    let selectedDate = $state(null);
    $effect(() => {
        // Default selection: today if in current month, otherwise first of month.
        const today = new Date();
        const sameMonth = today.getFullYear() === current.getFullYear() && today.getMonth() === current.getMonth();
        selectedDate = sameMonth ? ymd(today) : ymd(new Date(current.getFullYear(), current.getMonth(), 1));
    });

    let dayEvents = $derived.by(() => {
        if (!selectedDate) return [];
        return eventsByDay.get(selectedDate) || [];
    });

    let monthCells = $derived.by(() => {
        const year = current.getFullYear();
        const month = current.getMonth();
        const first = new Date(year, month, 1);
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const leading = weekdayIndex(first);
        const total = Math.ceil((leading + daysInMonth) / 7) * 7;

        const cells = [];
        for (let i = 0; i < total; i++) {
            const dayNum = i - leading + 1;
            if (dayNum < 1 || dayNum > daysInMonth) {
                cells.push(null);
            } else {
                const d = new Date(year, month, dayNum);
                cells.push({
                    key: ymd(d),
                    day: dayNum,
                });
            }
        }
        return cells;
    });

    const weekdays = ["Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom"];
</script>

<MemberLayout title="Eventi">
    {#snippet headerActions()}
        <div class="flex items-center gap-1">
            <Button variant="ghost" size="icon" onclick={previousMonth} aria-label="Mese precedente">
                <ChevronLeft class="size-5" />
            </Button>
            <div class="min-w-0 text-sm font-medium capitalize truncate max-w-[12rem]">{monthLabel}</div>
            <Button variant="ghost" size="icon" onclick={nextMonth} aria-label="Mese successivo">
                <ChevronRight class="size-5" />
            </Button>
        </div>
    {/snippet}

    <div class="space-y-4">
        <Card.Root class="overflow-hidden">
            <Card.Content class="p-0">
                <div class="px-4 pt-4 pb-3">
                    <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-muted-foreground">
                        {#each weekdays as w (w)}
                            <div class="py-1">{w}</div>
                        {/each}
                    </div>
                    <div class="grid grid-cols-7 gap-1">
                        {#each monthCells as cell, idx (cell?.key || `empty-${idx}`)}
                            {#if !cell}
                                <div class="h-11"></div>
                            {:else}
                                {@const hasEvents = eventsByDay.has(cell.key)}
                                {@const isSelected = selectedDate === cell.key}
                                <button
                                    type="button"
                                    class={[
                                        "h-11 rounded-md border text-sm font-medium",
                                        "bg-background hover:bg-accent/50 transition-colors",
                                        isSelected ? "bg-primary text-primary-foreground border-primary" : "border-border",
                                    ].join(" ")}
                                    onclick={() => (selectedDate = cell.key)}
                                >
                                    <div class="flex h-full flex-col items-center justify-center gap-1">
                                        <div>{cell.day}</div>
                                        {#if hasEvents}
                                            <div class={isSelected ? "h-1 w-6 rounded-full bg-primary-foreground/70" : "h-1 w-6 rounded-full bg-primary/60"}></div>
                                        {:else}
                                            <div class="h-1 w-6"></div>
                                        {/if}
                                    </div>
                                </button>
                            {/if}
                        {/each}
                    </div>
                </div>
            </Card.Content>
        </Card.Root>

        <div class="space-y-3">
            {#if dayEvents?.length}
                <div class="text-sm font-medium">
                    Eventi del giorno
                </div>
                {#each dayEvents as e (e.id)}
                    <Card.Root>
                        <Card.Content class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold truncate">{e.title}</div>
                                    <div class="text-xs text-muted-foreground">
                                        {fmtDateTime(e.start_date)} → {fmtDateTime(e.end_date)}
                                    </div>
                                </div>
                                <Badge variant={e.type === "festival" ? "secondary" : e.type === "meeting" ? "outline" : "default"}>
                                    {typeLabel(e.type)}
                                </Badge>
                            </div>
                        </Card.Content>
                    </Card.Root>
                {/each}
            {:else}
                <div class="text-sm text-muted-foreground">
                    Nessun evento per il giorno selezionato.
                </div>
            {/if}
        </div>
    </div>
</MemberLayout>


