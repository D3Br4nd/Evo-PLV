<script>
  import ThemeToggle from "@/lib/components/theme-toggle.svelte";
  import CookieConsent from "@/lib/components/cookie-consent.svelte";
  import { cn } from "@/lib/utils/cn";
  import { buttonVariants } from "@/lib/components/ui/button";
  let { title, children } = $props();

  const PRIVACY_URL = "https://www.prolocoventicano.com/privacy-policy";
  const COOKIE_URL = "https://www.prolocoventicano.com/cookie-policy";
</script>

<svelte:head>
  <title>{title}</title>
</svelte:head>

<div class="min-h-screen bg-background text-foreground">
  <header class="border-b">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
      <a href="/" class="flex items-center gap-3">
        <img src="/logo.png" alt="Pro Loco Venticanese" class="h-10 w-10" />
        <span class="font-semibold tracking-tight">Pro Loco Venticanese Evolution</span>
      </a>

      <div class="flex items-center gap-2">
        <a href="/login" class={cn(buttonVariants({ variant: "outline", size: "sm" }))}>Accedi</a>
        <ThemeToggle />
      </div>
    </div>
  </header>

  <main class="px-4 py-10 sm:px-6">
    {@render children?.()}
  </main>

  <footer class="border-t">
    <div class="mx-auto max-w-6xl space-y-4 px-4 py-8 text-sm sm:px-6">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-muted-foreground">
          © {new Date().getFullYear()} Pro Loco Venticanese
        </p>
        <div class="flex flex-wrap gap-x-4 gap-y-2">
          <a href={PRIVACY_URL} target="_blank" rel="noreferrer" class="text-muted-foreground hover:text-foreground hover:underline">
            Privacy Policy
          </a>
          <a href={COOKIE_URL} target="_blank" rel="noreferrer" class="text-muted-foreground hover:text-foreground hover:underline">
            Cookie Policy
          </a>
        </div>
      </div>
      <p class="text-xs text-muted-foreground">
        Questa piattaforma utilizza cookie tecnici necessari al funzionamento e, se autorizzati, cookie non essenziali.
      </p>
    </div>
  </footer>

  <CookieConsent />
</div>


