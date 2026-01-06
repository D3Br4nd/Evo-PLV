<script>
    import { Link } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import PublicLayout from "@/layouts/PublicLayout.svelte";
    import { buttonVariants } from "@/lib/components/ui/button";
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import { Badge } from "@/lib/components/ui/badge";
    import { Separator } from "@/lib/components/ui/separator";
    import * as Tooltip from "@/lib/components/ui/tooltip/index.js";
    import * as Dialog from "@/lib/components/ui/dialog";
    import { cn } from "@/lib/utils/cn";
    import { CalendarDays, CreditCard, ShieldCheck, Users, Download, Info } from "lucide-svelte";
    import { toast } from "svelte-sonner";

    let deferredPrompt = $state(null);
    let isInstalled = $state(false);
    let isIos = $state(false);

    function computeInstalled() {
        try {
            const standalone =
                window.matchMedia?.("(display-mode: standalone)")?.matches ||
                window.navigator?.standalone === true;
            isInstalled = !!standalone;
        } catch (e) {
            isInstalled = false;
        }
    }

    async function installPwa() {
        computeInstalled();
        if (isInstalled) {
            toast.message("App già installata.");
            return;
        }

        if (deferredPrompt) {
            deferredPrompt.prompt();
            const choice = await deferredPrompt.userChoice;
            deferredPrompt = null;
            if (choice?.outcome === "accepted") toast.success("Installazione avviata.");
            else toast.message("Installazione annullata.");
            return;
        }

        // iOS Safari (no prompt)
        if (isIos) {
            toast.message("Su iPhone/iPad: Condividi → Aggiungi a Home.");
            return;
        }

        toast.message("Apri il menu del browser e scegli “Installa app”.");
    }

    function scrollToId(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    function scrollToHash() {
        const hash = typeof window !== "undefined" ? window.location.hash : "";
        const id = hash?.startsWith("#") ? decodeURIComponent(hash.slice(1)) : "";
        if (id) scrollToId(id);
    }

    function handleComeFunzionaClick(e) {
        e.preventDefault();
        if (typeof window !== "undefined") {
            window.history.replaceState(null, "", "#come-funziona");
        }
        scrollToId("come-funziona");
    }

    onMount(() => {
        const ua = window.navigator?.userAgent?.toLowerCase?.() || "";
        isIos = /iphone|ipad|ipod/.test(ua);

        computeInstalled();

        const onBip = (e) => {
            // Chrome/Edge on Android: capture install prompt
            e.preventDefault();
            deferredPrompt = e;
        };

        const onInstalled = () => {
            deferredPrompt = null;
            computeInstalled();
        };

        window.addEventListener("beforeinstallprompt", onBip);
        window.addEventListener("appinstalled", onInstalled);

        // When loading /#come-funziona, the anchor target doesn't exist until the SPA renders.
        // So we scroll after mount, and keep supporting hash changes.
        scrollToHash();
        window.addEventListener("hashchange", scrollToHash);
        return () => {
            window.removeEventListener("hashchange", scrollToHash);
            window.removeEventListener("beforeinstallprompt", onBip);
            window.removeEventListener("appinstalled", onInstalled);
        };
    });
</script>

<PublicLayout title="Pro Loco Venticanese">
    <div class="mx-auto max-w-6xl space-y-12">
        <section class="grid gap-8 lg:grid-cols-2 lg:gap-12">
            <div class="space-y-5">
                <div class="flex flex-wrap gap-2">
                    <Badge variant="secondary">PWA</Badge>
                    <Badge variant="secondary">Eventi</Badge>
                    <Badge variant="secondary">Tesseramento</Badge>
                    <Badge variant="secondary">Check-in</Badge>
                </div>

                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                    Pro Loco Venticanese <span class="text-primary">Evolution</span>
                </h1>

                <p class="text-lg text-muted-foreground">
                    La piattaforma digitale per gestire <span class="font-medium text-foreground">eventi</span>,
                    <span class="font-medium text-foreground">soci</span> e
                    <span class="font-medium text-foreground">tesseramenti</span>, con una tessera sempre disponibile
                    sul telefono.
                </p>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <Link
                        href="/login"
                        class={cn(buttonVariants({ variant: "default" }), "sm:w-auto")}
                    >
                        Accedi
                    </Link>
                    <div class="flex items-center gap-2">
                        <Button variant="outline" onclick={installPwa} disabled={isInstalled}>
                            <Download class="mr-2 size-4" />
                            {isInstalled ? "App installata" : "Installa app"}
                        </Button>
                        <!-- Desktop: tooltip (hover). Mobile: dialog (tap). -->
                        <div class="hidden sm:block">
                            <Tooltip.Provider>
                                <Tooltip.Root>
                                    <Tooltip.Trigger>
                                        {#snippet child({ props })}
                                            <button
                                                {...props}
                                                type="button"
                                                class={cn(
                                                    buttonVariants({ variant: "ghost", size: "icon" }),
                                                    "h-10 w-10"
                                                )}
                                                aria-label="Istruzioni installazione"
                                            >
                                                <Info class="size-4" />
                                            </button>
                                        {/snippet}
                                    </Tooltip.Trigger>
                                    <Tooltip.Content class="max-w-xs" side="bottom" sideOffset={8}>
                                        <div class="space-y-3 text-sm">
                                            <div class="font-medium">Installazione PWA</div>
                                            <div class="space-y-2">
                                                <div class="font-medium text-xs text-muted-foreground">Android (Chrome)</div>
                                                <div class="text-xs text-muted-foreground">
                                                    Tocca <span class="font-medium text-foreground">Installa app</span> oppure:
                                                    menu ⋮ → <span class="font-medium text-foreground">Installa app</span>.
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <div class="font-medium text-xs text-muted-foreground">iPhone/iPad (Safari)</div>
                                                <div class="text-xs text-muted-foreground">
                                                    Tocca <span class="font-medium text-foreground">Condividi</span> →
                                                    <span class="font-medium text-foreground">Aggiungi a Home</span>.
                                                </div>
                                            </div>
                                        </div>
                                    </Tooltip.Content>
                                </Tooltip.Root>
                            </Tooltip.Provider>
                        </div>

                        <div class="sm:hidden">
                            <Dialog.Root>
                                <Dialog.Trigger>
                                    {#snippet child({ props })}
                                        <button
                                            {...props}
                                            type="button"
                                            class={cn(
                                                buttonVariants({ variant: "ghost", size: "icon" }),
                                                "h-10 w-10"
                                            )}
                                            aria-label="Istruzioni installazione"
                                        >
                                            <Info class="size-4" />
                                        </button>
                                    {/snippet}
                                </Dialog.Trigger>
                                <Dialog.Content class="max-w-md">
                                    <Dialog.Header>
                                        <Dialog.Title>Installazione PWA</Dialog.Title>
                                        <Dialog.Description>
                                            Procedura di installazione su Android e iOS.
                                        </Dialog.Description>
                                    </Dialog.Header>

                                    <div class="mt-4 space-y-4 text-sm">
                                        <div class="space-y-1">
                                            <div class="font-medium">Android (Chrome)</div>
                                            <div class="text-muted-foreground">
                                                Tocca <span class="font-medium text-foreground">Installa app</span> oppure:
                                                menu ⋮ → <span class="font-medium text-foreground">Installa app</span>.
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="font-medium">iPhone/iPad (Safari)</div>
                                            <div class="text-muted-foreground">
                                                Tocca <span class="font-medium text-foreground">Condividi</span> →
                                                <span class="font-medium text-foreground">Aggiungi a Home</span>.
                                            </div>
                                        </div>
                                    </div>

                                    <Dialog.Footer class="mt-6">
                                        <Dialog.Close>
                                            {#snippet child({ props })}
                                                <Button {...props} variant="outline">Chiudi</Button>
                                            {/snippet}
                                        </Dialog.Close>
                                    </Dialog.Footer>
                                </Dialog.Content>
                            </Dialog.Root>
                        </div>
                    </div>
                    <a
                        href="#come-funziona"
                        class={cn(buttonVariants({ variant: "outline" }), "sm:w-auto")}
                        onclick={handleComeFunzionaClick}
                    >
                        Come funziona
                    </a>
                </div>

                <p class="text-sm text-muted-foreground">
                    La creazione degli account è gestita dalla segreteria (niente registrazione pubblica).
                </p>
            </div>

            <Card.Root class="overflow-hidden">
                <Card.Header>
                    <Card.Title>In breve</Card.Title>
                    <Card.Description>
                        Tutto quello che serve per una Pro Loco moderna, in un’unica dashboard.
                    </Card.Description>
                </Card.Header>
                <Card.Content class="space-y-5">
                    <div class="grid gap-3">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 rounded-md border bg-background p-2">
                                <CalendarDays class="size-4" />
                            </div>
                            <div>
                                <p class="font-medium">Calendario eventi</p>
                                <p class="text-sm text-muted-foreground">
                                    Pianificazione, presenze e report in modo semplice.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 rounded-md border bg-background p-2">
                                <Users class="size-4" />
                            </div>
                            <div>
                                <p class="font-medium">Gestione soci</p>
                                <p class="text-sm text-muted-foreground">
                                    Anagrafica, stato iscrizione e ruoli (segreteria/direzione).
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 rounded-md border bg-background p-2">
                                <CreditCard class="size-4" />
                            </div>
                            <div>
                                <p class="font-medium">Tessera digitale</p>
                                <p class="text-sm text-muted-foreground">
                                    Sempre a portata di mano, perfetta per controlli e accessi.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 rounded-md border bg-background p-2">
                                <ShieldCheck class="size-4" />
                            </div>
                            <div>
                                <p class="font-medium">Sicurezza & controllo</p>
                                <p class="text-sm text-muted-foreground">
                                    Accessi e permessi gestiti centralmente dal backend.
                                </p>
                            </div>
                        </div>
                    </div>
                </Card.Content>
            </Card.Root>
        </section>

        <Separator />

        <section id="come-funziona" class="space-y-6 scroll-mt-24">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Come funziona</h2>
                <p class="text-muted-foreground">
                    Un flusso semplice: la segreteria crea gli account, poi ognuno ha la sua area dedicata.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card.Root>
                    <Card.Header>
                        <Card.Title>1. Creazione account</Card.Title>
                        <Card.Description>Dalla segreteria</Card.Description>
                    </Card.Header>
                    <Card.Content class="text-sm text-muted-foreground">
                        Le registrazioni non sono pubbliche: la segreteria abilita gli utenti e assegna i ruoli.
                    </Card.Content>
                </Card.Root>
                <Card.Root>
                    <Card.Header>
                        <Card.Title>2. Gestione & operatività</Card.Title>
                        <Card.Description>Dashboard e strumenti</Card.Description>
                    </Card.Header>
                    <Card.Content class="text-sm text-muted-foreground">
                        Eventi, check-in, progetti e contenuti: tutto centralizzato e sempre tracciabile.
                    </Card.Content>
                </Card.Root>
                <Card.Root>
                    <Card.Header>
                        <Card.Title>3. Tessera digitale</Card.Title>
                        <Card.Description>Per i soci</Card.Description>
                    </Card.Header>
                    <Card.Content class="text-sm text-muted-foreground">
                        Ogni socio accede alla propria tessera e alle informazioni utili direttamente dal telefono.
                    </Card.Content>
                </Card.Root>
            </div>

            <Separator />

            <div class="grid gap-4 md:grid-cols-3">
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Segreteria</Card.Title>
                        <Card.Description>Gestione soci e tesseramenti</Card.Description>
                    </Card.Header>
                    <Card.Content class="text-sm text-muted-foreground">
                        Creazione account, verifica dati, rinnovi e stato iscrizioni.
                    </Card.Content>
                </Card.Root>
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Direzione</Card.Title>
                        <Card.Description>Eventi e operatività</Card.Description>
                    </Card.Header>
                    <Card.Content class="text-sm text-muted-foreground">
                        Calendario, check-in e panoramica rapida delle attività.
                    </Card.Content>
                </Card.Root>
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Soci</Card.Title>
                        <Card.Description>Tessera digitale</Card.Description>
                    </Card.Header>
                    <Card.Content class="text-sm text-muted-foreground">
                        Accesso alla propria tessera e informazioni utili.
                    </Card.Content>
                </Card.Root>
            </div>
        </section>

        <Separator />

        <section class="grid gap-6 lg:grid-cols-2 lg:items-center">
            <div class="space-y-2">
                <h2 class="text-2xl font-semibold tracking-tight">Pronti a partire?</h2>
                <p class="text-muted-foreground">
                    Se hai già un account, accedi. Se ti serve l’abilitazione, contatta la segreteria: la creazione utenti
                    avviene dal backend.
                </p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
                <Link href="/login" class={cn(buttonVariants({ variant: "default" }), "sm:w-auto")}>
                    Accedi
                </Link>
                <a
                    href="https://www.prolocoventicano.com/"
                    target="_blank"
                    rel="noreferrer"
                    class={cn(buttonVariants({ variant: "outline" }), "sm:w-auto")}
                >
                    Vai al sito ufficiale
                </a>
            </div>
        </section>
    </div>
</PublicLayout>
