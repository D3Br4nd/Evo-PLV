<script>
    import PublicLayout from "@/layouts/PublicLayout.svelte";
    import { Separator } from "@/lib/components/ui/separator";
    import { Button } from "@/lib/components/ui/button";

    const PRIVACY_URL = "https://www.prolocoventicano.com/privacy-policy";
</script>

<PublicLayout title="Privacy Policy">
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold tracking-tight">Privacy Policy</h1>
            <p class="text-muted-foreground">
                Informativa privacy della piattaforma Pro Loco Venticanese Evolution.
            </p>
        </div>

        <Separator />

        <div class="space-y-4">
            <p class="text-muted-foreground">
                Per garantire un testo sempre aggiornato e coerente con il sito istituzionale,
                l’informativa completa è pubblicata sul sito principale.
            </p>

            <Button href={PRIVACY_URL} target="_blank" rel="noreferrer">
                Apri Privacy Policy su www.prolocoventicano.com
            </Button>
        </div>
    </div>
</PublicLayout>


