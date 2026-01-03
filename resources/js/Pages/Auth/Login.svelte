<script>
    import { useForm } from "@inertiajs/svelte";
    import { Mail, Lock, LogIn } from "lucide-svelte";
    import PublicLayout from "@/layouts/PublicLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import * as Card from "@/lib/components/ui/card";

    // Form state
    const form = useForm({
        email: "",
        password: "",
        remember: false,
    });

    function handleSubmit(e) {
        e.preventDefault();
        $form.post("/login");
    }
</script>

<PublicLayout title="Accedi">
    <div class="mx-auto w-full max-w-md">
        <Card.Root>
            <Card.Header class="text-center">
                <Card.Title>Accedi</Card.Title>
                <Card.Description>Accedi alla piattaforma</Card.Description>
            </Card.Header>
            <Card.Content>
                <form onsubmit={handleSubmit} class="space-y-4">
                    <div class="space-y-1.5">
                        <label for="email" class="text-sm font-medium">Email</label>
                        <div class="relative">
                            <Mail
                                class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="email"
                                type="email"
                                bind:value={$form.email}
                                required
                                placeholder="admin@prolocoventicanese.it"
                                class="pl-9"
                            />
                        </div>
                        {#if $form.errors.email}
                            <p class="text-sm text-destructive">{$form.errors.email}</p>
                        {/if}
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="text-sm font-medium"
                            >Password</label
                        >
                        <div class="relative">
                            <Lock
                                class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="password"
                                type="password"
                                bind:value={$form.password}
                                required
                                placeholder="••••••••"
                                class="pl-9"
                            />
                        </div>
                        {#if $form.errors.password}
                            <p class="text-sm text-destructive">
                                {$form.errors.password}
                            </p>
                        {/if}
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            id="remember"
                            type="checkbox"
                            bind:checked={$form.remember}
                            class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                        />
                        <label for="remember" class="text-sm">Ricordami</label>
                    </div>

                    <Button type="submit" disabled={$form.processing} class="w-full">
                        {#if $form.processing}
                            Accesso in corso...
                        {:else}
                            <LogIn class="size-4" /> Accedi
                        {/if}
                    </Button>
                </form>
            </Card.Content>
            <Card.Footer class="justify-center">
                <p class="text-sm text-muted-foreground">
                    Non hai un account?
                    <a href="/register" class="font-medium text-primary hover:underline"
                        >Registrati</a
                    >
                </p>
            </Card.Footer>
        </Card.Root>
    </div>
</PublicLayout>
