<script>
    import PublicLayout from "@/layouts/PublicLayout.svelte";
    import { Separator } from "@/lib/components/ui/separator";
    import { Button } from "@/lib/components/ui/button";

    const COOKIE_URL = "https://www.prolocoventicano.com/cookie-policy";
</script>

<PublicLayout title="Cookie Policy">
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold tracking-tight">Cookie Policy</h1>
            <p class="text-muted-foreground">
                Informativa cookie della piattaforma Pro Loco Venticanese Evolution.
            </p>
        </div>

        <Separator />

        <div class="space-y-4">
            <p class="text-muted-foreground">
                La Cookie Policy completa è pubblicata sul sito principale, così da mantenere un’unica fonte aggiornata.
            </p>

            <Button href={COOKIE_URL} target="_blank" rel="noreferrer">
                Apri Cookie Policy su www.prolocoventicano.com
            </Button>
        </div>
    </div>
</PublicLayout>


