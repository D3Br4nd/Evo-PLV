<script>
    /* eslint-disable */
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Button } from "@/lib/components/ui/button";
    import { Badge } from "@/lib/components/ui/badge";
    import { router, Link } from "@inertiajs/svelte";
    import { toast } from "svelte-sonner";

    let { notifications, vapidPublicKey, hasPushSubscription } = $props();

    let pushEnabled = $state(false);
    let processing = $state(false);

    $effect(() => {
        pushEnabled = !!hasPushSubscription;
    });

    function urlBase64ToUint8Array(base64String) {
        const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, "+")
            .replace(/_/g, "/");
        const rawData = atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i)
            outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }

    async function enablePush() {
        if (!("Notification" in window) || !("serviceWorker" in navigator)) {
            toast.error("Notifiche non supportate su questo browser.");
            return;
        }
        if (!vapidPublicKey) {
            toast.error("VAPID_PUBLIC_KEY non configurata sul server.");
            return;
        }
        processing = true;
        try {
            const permission = await Notification.requestPermission();
            if (permission !== "granted") {
                toast.message("Permesso notifiche non concesso.");
                return;
            }
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
            });
            
            // Convert subscription to JSON and add contentEncoding
            const subscriptionData = sub.toJSON();
            subscriptionData.contentEncoding = PushManager.supportedContentEncodings?.[0] || 'aesgcm';
            
            console.log('Sending push subscription:', subscriptionData);
            
            const res = await fetch("/me/push-subscriptions", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                },
                body: JSON.stringify(subscriptionData),
            });
            if (!res.ok) {
                pushEnabled = false;
                toast.error("Errore durante la registrazione sul server.");
                return;
            }
            pushEnabled = true;
            toast.success("Notifiche abilitate.");
        } catch (e) {
            toast.error("Impossibile abilitare le notifiche.");
        } finally {
            processing = false;
        }
    }

    async function disablePush() {
        if (!("serviceWorker" in navigator)) {
            toast.error("Notifiche non supportate su questo browser.");
            return;
        }
        processing = true;
        try {
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            if (sub) await sub.unsubscribe();
            const res = await fetch("/me/push-subscriptions", {
                method: "DELETE",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                },
            });
            if (!res.ok) {
                toast.error("Errore durante la disattivazione sul server.");
                return;
            }
            pushEnabled = false;
            toast.message("Notifiche disabilitate.");
        } catch (e) {
            toast.error("Impossibile disabilitare le notifiche.");
        } finally {
            processing = false;
        }
    }

    function destroyNotification(id) {
        router.delete(`/me/notifications/${id}`, { preserveScroll: true });
    }

    let displayNotifications = $derived.by(() => {
        if (!notifications?.data) return [];
        
        // Group by project_id or broadcast_id to show only the latest
        const map = new Map();
        
        notifications.data.forEach(n => {
            const projectId = n.data?.project_id;
            const broadcastId = n.data?.broadcast_id;
            const key = projectId ? `project_${projectId}` : (broadcastId ? `broadcast_${broadcastId}` : n.id);
            
            if (!map.has(key)) {
                map.set(key, n);
            } else {
                // If the one in map is older (though list usually sorted desc), replace it?
                // Actually Laravel usually sends them latest first, so map.has(key) is already the latest.
            }
        });
        
        return Array.from(map.values());
    });

    function titleOf(n) {
        const data = n?.data || {};
        return data?.title || "Notifica";
    }

    function bodyOf(n) {
        const data = n?.data || {};
        return data?.body || data?.message || "";
    }

    function urlOf(n) {
        return n?.data?.url || null;
    }
</script>

<MemberLayout title="Notifiche">
    {#snippet headerActions()}{/snippet}

    <div class="space-y-4">
        <Card.Root>
            <Card.Header>
                <Card.Title>Notifiche</Card.Title>
                <Card.Description
                    >Le notifiche restano qui finché non le elimini.</Card.Description
                >
            </Card.Header>
            <Card.Content class="space-y-3">
                {#if displayNotifications.length}
                    {#each displayNotifications as n (n.id)}
                        {@const type = n.data?.type || 'unknown'}
                        <div class={[
                            "rounded-lg border p-3 transition-all duration-200",
                            type === 'broadcast' ? "border-zinc-800 bg-zinc-50 dark:border-zinc-200 dark:bg-zinc-900/50" : "",
                            type === 'committee_post' ? "border-zinc-300 bg-zinc-100/30 dark:border-zinc-700 dark:bg-zinc-800/30" : "",
                            type === 'event' ? "border-zinc-200 bg-white dark:border-zinc-800 dark:bg-black" : "",
                            type === 'project_update' ? "border-blue-200 bg-blue-50/50 dark:border-blue-900 dark:bg-blue-950/30" : "",
                            !n.read_at ? "shadow-md ring-1 ring-primary/10 -translate-y-[1px]" : "opacity-80"
                        ].join(" ")}>
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    {#if urlOf(n)}
                                        <Link
                                            href={urlOf(n)}
                                            class="block hover:opacity-80 transition-opacity"
                                        >
                                            <div class="flex items-center gap-2 mb-1">
                                                {#if type === 'broadcast'}
                                                    <Badge variant="default" class="text-[9px] h-4 px-1 uppercase">Broadcast</Badge>
                                                {:else if type === 'committee_post'}
                                                    <Badge variant="secondary" class="text-[9px] h-4 px-1 uppercase">Comitato</Badge>
                                                {:else if type === 'event'}
                                                    <Badge variant="outline" class="text-[9px] h-4 px-1 uppercase">Evento</Badge>
                                                {:else if type === 'project_update'}
                                                    <Badge variant="secondary" class="text-[9px] h-4 px-1 uppercase border-blue-400 text-blue-700 bg-blue-50">Progetto</Badge>
                                                {/if}
                                            </div>
                                            <div class="font-medium truncate">
                                                {titleOf(n)}
                                            </div>
                                            {#if bodyOf(n)}
                                                <div
                                                    class="mt-1 text-xs text-muted-foreground line-clamp-2"
                                                >
                                                    {bodyOf(n)}
                                                </div>
                                            {/if}
                                        </Link>
                                    {:else}
                                        <div class="flex items-center gap-2 mb-1">
                                            {#if type === 'broadcast'}
                                                <Badge variant="default" class="text-[9px] h-4 px-1 uppercase">Broadcast</Badge>
                                            {:else if type === 'committee_post'}
                                                <Badge variant="secondary" class="text-[9px] h-4 px-1 uppercase">Comitato</Badge>
                                            {:else if type === 'event'}
                                                <Badge variant="outline" class="text-[9px] h-4 px-1 uppercase">Evento</Badge>
                                            {:else if type === 'project_update'}
                                                <Badge variant="secondary" class="text-[9px] h-4 px-1 uppercase border-blue-400 text-blue-700 bg-blue-50">Progetto</Badge>
                                            {/if}
                                        </div>
                                        <div class="font-medium truncate">
                                            {titleOf(n)}
                                        </div>
                                        {#if bodyOf(n)}
                                            <div
                                                class="mt-1 text-xs text-muted-foreground"
                                            >
                                                {bodyOf(n)}
                                            </div>
                                        {/if}
                                    {/if}
                                    <div
                                        class="mt-2 text-[11px] text-muted-foreground"
                                    >
                                        {new Date(n.created_at).toLocaleString(
                                            "it-IT",
                                        )}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    {#if n.read_at}
                                        <Badge variant="secondary">Letta</Badge>
                                    {:else}
                                        <Badge variant="outline">Nuova</Badge>
                                    {/if}
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        onclick={() =>
                                            destroyNotification(n.id)}
                                    >
                                        Elimina
                                    </Button>
                                </div>
                            </div>
                        </div>
                    {/each}
                {:else}
                    <div class="text-sm text-muted-foreground">
                        Nessuna notifica.
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>
</MemberLayout>
