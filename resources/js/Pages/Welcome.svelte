<script>
    import { Link } from "@inertiajs/svelte";
    let { ...props } = $props();
</script>

<svelte:head>
    <title>Welcome</title>
</svelte:head>

<div
    class="min-h-screen bg-background text-foreground flex flex-col items-center justify-center p-4"
>
    <div class="max-w-xl w-full text-center space-y-8">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img
                src="/logo.png"
                alt="Pro Loco Venticanese"
                class="h-24 w-auto"
            />
        </div>

        <h1 class="text-4xl font-bold tracking-tight lg:text-5xl">
            Pro Loco Venticanese <span class="text-primary">Evolution</span>
        </h1>

        <p class="text-lg text-muted-foreground">
            Benvenuti nella nuova piattaforma digitale per la gestione di eventi
            e tesseramenti.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
                class="rounded-lg border bg-card text-card-foreground shadow-sm p-6"
            >
                <h3 class="text-lg font-semibold">Prossimo Evento</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    46ª Fiera Campionaria
                </p>
                <div class="mt-4">
                    <Link
                        href="/login"
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full"
                    >
                        Dettagli
                    </Link>
                </div>
            </div>

            <div
                class="rounded-lg border bg-card text-card-foreground shadow-sm p-6"
            >
                <h3 class="text-lg font-semibold">Tesseramento 2025</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Unisciti a noi per il nuovo anno.
                </p>
                <div class="mt-4">
                    <Link
                        href="/login"
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full"
                    >
                        Accedi
                    </Link>
                </div>
            </div>
        </div>
    </div>
</div>
