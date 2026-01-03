<script lang="ts">
  import { Sun, Moon } from "lucide-svelte";
  import { Button } from "@/lib/components/ui/button";

  const key = "plv_theme";

  let theme = $state<"light" | "dark">("light");

  function apply(next: "light" | "dark") {
    theme = next;
    try {
      localStorage.setItem(key, next);
    } catch {}
    document.documentElement.classList.toggle("dark", next === "dark");
  }

  // Initialize from DOM (script in app.blade.php already ran)
  $effect(() => {
    theme = document.documentElement.classList.contains("dark") ? "dark" : "light";
  });
</script>

<Button
  variant="outline"
  size="icon"
  aria-label="Cambia tema"
  onclick={() => apply(theme === "dark" ? "light" : "dark")}
>
  {#if theme === "dark"}
    <Moon />
  {:else}
    <Sun />
  {/if}
</Button>


