<script>
    let { page } = $props();
    import PublicLayout from "@/layouts/PublicLayout.svelte";
</script>

<PublicLayout title={page.title}>
    <div class="mx-auto max-w-3xl space-y-4">
        <h1 class="text-3xl font-bold">{page.title}</h1>
        {#if page.excerpt}
            <p class="text-muted-foreground">{page.excerpt}</p>
        {/if}
        <div
            class="prose prose-zinc dark:prose-invert max-w-none leading-relaxed"
        >
            {@html page.body}
        </div>
    </div>
</PublicLayout>

<style>
    :global(.prose ul) {
        list-style-type: disc;
        padding-left: 1.5rem;
    }
    :global(.prose ol) {
        list-style-type: decimal;
        padding-left: 1.5rem;
    }
    :global(.prose a) {
        color: hsl(var(--primary));
        text-decoration: underline;
    }
    :global(.prose p) {
        margin-bottom: 0.75rem;
    }
</style>
