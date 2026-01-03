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
        <div class="max-w-none whitespace-pre-wrap leading-relaxed">
            {page.body}
        </div>
    </div>
</PublicLayout>


