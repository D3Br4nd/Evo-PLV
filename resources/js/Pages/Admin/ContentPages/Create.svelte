<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import ChevronLeft from "lucide-svelte/icons/chevron-left";
    import Editor from "./Editor.svelte";
    import { router } from "@inertiajs/svelte";
</script>

<AdminLayout
    title="Nuova Pagina"
    breadcrumbs={[{ label: "Pagine del Sito", href: "/admin/content-pages" }]}
>
    <div class="space-y-6">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div class="flex items-center gap-4">
                <Button
                    variant="ghost"
                    size="icon"
                    class="h-9 w-9"
                    onclick={() => router.get("/admin/content-pages")}
                >
                    <ChevronLeft class="size-5" />
                </Button>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Nuova Pagina
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Crea un nuovo contenuto informativo per il sito.
                    </p>
                </div>
            </div>
        </div>

        <Editor />
    </div>
</AdminLayout>
