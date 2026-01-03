<script>
    import { Link } from "@inertiajs/svelte";
    import PublicLayout from "@/layouts/PublicLayout.svelte";
    import { buttonVariants } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import { cn } from "@/lib/utils/cn";
</script>

<PublicLayout title="Pro Loco Venticanese">
    <div class="mx-auto max-w-3xl space-y-8">
        <div class="text-center space-y-2">
            <h1 class="text-4xl font-bold tracking-tight lg:text-5xl">
                Pro Loco Venticanese <span class="text-primary">Evolution</span>
            </h1>
            <p class="text-lg text-muted-foreground">
                Benvenuti nella piattaforma digitale per eventi e tesseramenti.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Prossimo evento</Card.Title>
                    <Card.Description>46ª Fiera Campionaria</Card.Description>
                </Card.Header>
                <Card.Footer>
                    <Link
                        href="/login"
                        class={cn(buttonVariants({ variant: "default" }), "w-full")}
                    >
                        Dettagli
                    </Link>
                </Card.Footer>
            </Card.Root>

            <Card.Root>
                <Card.Header>
                    <Card.Title>Tesseramento 2025</Card.Title>
                    <Card.Description>Unisciti a noi per il nuovo anno.</Card.Description>
                </Card.Header>
                <Card.Footer>
                    <Link
                        href="/login"
                        class={cn(buttonVariants({ variant: "outline" }), "w-full")}
                    >
                        Accedi
                    </Link>
                </Card.Footer>
            </Card.Root>
        </div>
    </div>
</PublicLayout>
