<script>
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import { ArrowLeft, FileText, ExternalLink } from "lucide-svelte";
    import { Link } from "@inertiajs/svelte";

    let { pages } = $props();
</script>

<MemberLayout title="Informazioni">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <Button
                variant="ghost"
                size="icon"
                onclick={() => window.history.back()}
            >
                <ArrowLeft class="size-5" />
            </Button>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl font-bold tracking-tight">Informazioni</h1>
                <p class="text-sm text-muted-foreground truncate">
                    Pagine informative e comunicazioni
                </p>
            </div>
        </div>

        {#if pages.length === 0}
            <Card.Root>
                <Card.Content class="p-8 text-center text-muted-foreground">
                    <FileText class="size-12 mx-auto mb-4 opacity-50" />
                    <p>Nessuna pagina pubblicata al momento.</p>
                </Card.Content>
            </Card.Root>
        {:else}
            <div class="space-y-3">
                {#each pages as page (page.id)}
                    <Link
                        href={`/me/content/${page.slug}`}
                        class="block rounded-xl border bg-card hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        <div class="flex items-start gap-4 px-4 py-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg border bg-amber-50 dark:bg-amber-950/30 flex-shrink-0"
                            >
                                <FileText class="size-5 text-amber-600" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold">{page.title}</div>
                                {#if page.excerpt}
                                    <div
                                        class="text-sm text-muted-foreground line-clamp-2 mt-1"
                                    >
                                        {page.excerpt}
                                    </div>
                                {/if}
                                <div class="text-xs text-muted-foreground mt-2">
                                    Pubblicato il {new Date(
                                        page.published_at,
                                    ).toLocaleDateString("it-IT", {
                                        day: "numeric",
                                        month: "long",
                                        year: "numeric",
                                    })}
                                </div>
                            </div>
                        </div>
                    </Link>
                {/each}
            </div>
        {/if}
    </div>
</MemberLayout>
