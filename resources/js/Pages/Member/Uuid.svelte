<script>
    /* eslint-disable */
    import { page } from "@inertiajs/svelte";
    import QRCode from "qrcode";
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Button } from "@/lib/components/ui/button";

    let user = $derived($page.props.auth?.user);
    let uuid = $derived(user?.id);
    let qrDataUrl = $state(null);

    async function generate() {
        if (!uuid) return;
        qrDataUrl = await QRCode.toDataURL(uuid, {
            width: 320,
            margin: 2,
            color: { dark: "#000000", light: "#FFFFFF" },
        });
    }

    $effect(() => {
        generate();
    });
</script>

<MemberLayout title="QR UUID">
    <div class="space-y-4">
        <Card.Root>
            <Card.Header>
                <Card.Title>Il tuo UUIDv7</Card.Title>
                <Card.Description>Mostra questo QR quando richiesto.</Card.Description>
            </Card.Header>
            <Card.Content class="space-y-4">
                <div class="flex justify-center">
                    {#if qrDataUrl}
                        <img src={qrDataUrl} alt="QR UUID" class="rounded bg-white p-2" />
                    {:else}
                        <div class="text-sm text-muted-foreground">Generazione QR...</div>
                    {/if}
                </div>
                <div class="rounded-md border bg-background px-3 py-2 font-mono text-xs break-all">
                    {uuid}
                </div>
                <Button
                    variant="secondary"
                    onclick={() => navigator.clipboard?.writeText(uuid)}
                    disabled={!uuid}
                >
                    Copia UUID
                </Button>
            </Card.Content>
        </Card.Root>
    </div>
</MemberLayout>


