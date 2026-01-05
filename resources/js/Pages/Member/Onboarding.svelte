<script>
    /* eslint-disable */
    import { page } from "@inertiajs/svelte";
    import { router } from "@inertiajs/svelte";

    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import * as Card from "@/lib/components/ui/card";

    let { user, vapidPublicKey, hasPushSubscription } = $props();
    let flash = $derived($page.props.flash);

    // ---- PWA install prompt ----
    let deferredPrompt = $state(null);
    let canInstall = $derived(!!deferredPrompt);

    if (typeof window !== "undefined") {
        window.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });
    }

    async function installPwa() {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
    }

    // ---- Password ----
    let pwd = $state({
        current_password: "",
        password: "",
        password_confirmation: "",
    });

    function savePassword() {
        const payload = { ...pwd };
        // On first setup, backend doesn't require current_password
        if (user.must_set_password) delete payload.current_password;
        router.patch("/me/password", payload, { preserveScroll: true });
    }

    // ---- Push notifications ----
    function urlBase64ToUint8Array(base64String) {
        const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
        const rawData = atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }

    let notificationStatus = $state("");

    async function enableNotifications() {
        if (!("Notification" in window) || !("serviceWorker" in navigator)) {
            notificationStatus = "Notifiche non supportate su questo browser.";
            return;
        }
        if (!vapidPublicKey) {
            notificationStatus =
                "VAPID_PUBLIC_KEY non configurata sul server: impossibile registrare notifiche.";
            return;
        }

        const permission = await Notification.requestPermission();
        if (permission !== "granted") {
            notificationStatus = "Permesso notifiche non concesso.";
            return;
        }

        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
        });

        await fetch("/me/push-subscriptions", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
            },
            body: JSON.stringify(sub),
        });

        notificationStatus = "Notifiche abilitate.";
    }
</script>

<MemberLayout title="Onboarding">
    {#snippet headerActions()}
        <Button variant="outline" onclick={() => router.get("/me/card")}>
            Vai alla tessera
        </Button>
    {/snippet}

    <div class="max-w-xl space-y-6">
        <h2 class="text-2xl font-bold">Benvenuto</h2>

        {#if flash?.success}
            <div class="text-sm text-green-600 dark:text-green-400">{flash.success}</div>
        {/if}
        {#if flash?.error}
            <div class="text-sm text-destructive">{flash.error}</div>
        {/if}

        <Card.Root>
            <Card.Header>
                <Card.Title>1) Installa la PWA</Card.Title>
                <Card.Description>
                    Installa l’app sul telefono per accesso rapido e migliore esperienza.
                </Card.Description>
            </Card.Header>
            <Card.Content class="space-y-3">
                <div class="text-sm text-muted-foreground">
                    Se non vedi il bottone, usa “Aggiungi a schermata Home” dal menu del browser.
                </div>
                <Button onclick={installPwa} disabled={!canInstall}>
                    {canInstall ? "Installa" : "Installazione non disponibile"}
                </Button>
            </Card.Content>
        </Card.Root>

        <Card.Root>
            <Card.Header>
                <Card.Title>2) Abilita notifiche</Card.Title>
                <Card.Description>
                    Riceverai comunicazioni su eventi e aggiornamenti.
                </Card.Description>
            </Card.Header>
            <Card.Content class="space-y-3">
                {#if hasPushSubscription}
                    <div class="text-sm text-muted-foreground">
                        Notifiche: già abilitate su questo account.
                    </div>
                {/if}
                <Button onclick={enableNotifications}>
                    Abilita notifiche
                </Button>
                {#if notificationStatus}
                    <div class="text-sm text-muted-foreground">{notificationStatus}</div>
                {/if}
            </Card.Content>
        </Card.Root>

        <Card.Root>
            <Card.Header>
                <Card.Title>3) Imposta/Cambia password</Card.Title>
                <Card.Description>
                    Ti consigliamo di impostare una password personale.
                </Card.Description>
            </Card.Header>
            <Card.Content class="space-y-3">
                {#if !user.must_set_password}
                    <div>
                        <div class="text-xs text-muted-foreground mb-1">Password attuale</div>
                        <Input type="password" bind:value={pwd.current_password} />
                    </div>
                {/if}
                <div>
                    <div class="text-xs text-muted-foreground mb-1">Nuova password</div>
                    <Input type="password" bind:value={pwd.password} />
                </div>
                <div>
                    <div class="text-xs text-muted-foreground mb-1">Conferma nuova password</div>
                    <Input type="password" bind:value={pwd.password_confirmation} />
                </div>
                <Button onclick={savePassword}>Salva password</Button>
            </Card.Content>
        </Card.Root>
    </div>
</MemberLayout>


