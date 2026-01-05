<script>
    import { onMount } from "svelte";
    import { Button } from "@/lib/components/ui/button";
    import { cn } from "@/lib/utils/cn";

    const COOKIE_NAME = "plv:cookie-consent";
    const COOKIE_MAX_AGE = 60 * 60 * 24 * 180; // 180 days
    const PRIVACY_URL = "https://www.prolocoventicano.com/privacy-policy";
    const COOKIE_URL = "https://www.prolocoventicano.com/cookie-policy";

    let open = false;

    function getCookie(name) {
        if (typeof document === "undefined") return null;
        const match = document.cookie.match(new RegExp(`(?:^|; )${name.replace(/[-.*+?^${}()|[\\]\\\\]/g, "\\\\$&")}=([^;]*)`));
        return match ? decodeURIComponent(match[1]) : null;
    }

    function setCookie(name, value, maxAgeSeconds) {
        document.cookie = `${name}=${encodeURIComponent(value)}; path=/; max-age=${maxAgeSeconds}; samesite=lax`;
    }

    function acceptAll() {
        setCookie(COOKIE_NAME, "accepted", COOKIE_MAX_AGE);
        open = false;
    }

    function rejectNonEssential() {
        setCookie(COOKIE_NAME, "rejected", COOKIE_MAX_AGE);
        open = false;
    }

    onMount(() => {
        const existing = getCookie(COOKIE_NAME);
        open = !existing;
    });
</script>

{#if open}
    <div class="fixed inset-x-0 bottom-0 z-50 p-4 sm:p-6">
        <div
            class={cn(
                "mx-auto max-w-4xl rounded-lg border bg-background/95 p-4 shadow-lg backdrop-blur supports-[backdrop-filter]:bg-background/80",
                "flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            )}
        >
            <div class="space-y-1">
                <p class="text-sm font-medium">Cookie e privacy</p>
                <p class="text-sm text-muted-foreground">
                    Usiamo cookie tecnici per far funzionare la piattaforma. Puoi accettare o rifiutare quelli non essenziali.
                    <a href={COOKIE_URL} target="_blank" rel="noreferrer" class="font-medium text-primary hover:underline">Cookie Policy</a>
                    e
                    <a href={PRIVACY_URL} target="_blank" rel="noreferrer" class="font-medium text-primary hover:underline">Privacy Policy</a>.
                </p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end sm:whitespace-nowrap">
                <Button variant="outline" onclick={rejectNonEssential}>Rifiuta</Button>
                <Button onclick={acceptAll}>Accetta</Button>
            </div>
        </div>
    </div>
{/if}


