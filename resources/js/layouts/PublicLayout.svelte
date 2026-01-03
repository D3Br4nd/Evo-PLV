<script>
  import ThemeToggle from "@/lib/components/theme-toggle.svelte";
  let { title, children } = $props();
</script>

<svelte:head>
  <title>{title}</title>
</svelte:head>

<div class="min-h-screen bg-background text-foreground">
  <header class="flex items-center justify-between p-4 sm:p-6">
    <a href="/" class="flex items-center gap-3">
      <img src="/logo.png" alt="Pro Loco Venticanese" class="h-10 w-10" />
      <span class="font-semibold">Pro Loco Venticanese</span>
    </a>
    <ThemeToggle />
  </header>
  <main class="px-4 pb-10 sm:px-6">
    {@render children?.()}
  </main>
</div>


