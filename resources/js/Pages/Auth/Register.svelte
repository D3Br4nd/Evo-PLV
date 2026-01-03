<script>
    import { useForm } from "@inertiajs/svelte";
    import { Mail, Lock, User as UserIcon, UserPlus } from "lucide-svelte";
    import PublicLayout from "@/layouts/PublicLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import * as Card from "@/lib/components/ui/card";

    // Form state
    const form = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });

    function handleSubmit(e) {
        e.preventDefault();
        $form.post("/register");
    }
</script>

<PublicLayout title="Registrati">
    <div class="mx-auto w-full max-w-md">
        <Card.Root>
            <Card.Header class="text-center">
                <Card.Title>Registrati</Card.Title>
                <Card.Description>Crea il tuo account</Card.Description>
            </Card.Header>
            <Card.Content>
                <form onsubmit={handleSubmit} class="space-y-4">
                    <div class="space-y-1.5">
                        <label for="name" class="text-sm font-medium">Nome completo</label>
                        <div class="relative">
                            <UserIcon
                                class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="name"
                                type="text"
                                bind:value={$form.name}
                                required
                                placeholder="Mario Rossi"
                                class="pl-9"
                            />
                        </div>
                        {#if $form.errors.name}
                            <p class="text-sm text-destructive">{$form.errors.name}</p>
                        {/if}
                    </div>

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
                                placeholder="mario.rossi@example.com"
                                class="pl-9"
                            />
                        </div>
                        {#if $form.errors.email}
                            <p class="text-sm text-destructive">{$form.errors.email}</p>
                        {/if}
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="text-sm font-medium">Password</label>
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
                            <p class="text-sm text-destructive">{$form.errors.password}</p>
                        {/if}
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="text-sm font-medium">
                            Conferma password
                        </label>
                        <div class="relative">
                            <Lock
                                class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="password_confirmation"
                                type="password"
                                bind:value={$form.password_confirmation}
                                required
                                placeholder="••••••••"
                                class="pl-9"
                            />
                        </div>
                    </div>

                    <Button type="submit" disabled={$form.processing} class="w-full">
                        {#if $form.processing}
                            Registrazione in corso...
                        {:else}
                            <UserPlus class="size-4" /> Registrati
                        {/if}
                    </Button>
                </form>
            </Card.Content>
            <Card.Footer class="justify-center">
                <p class="text-sm text-muted-foreground">
                    Hai già un account?
                    <a href="/login" class="font-medium text-primary hover:underline"
                        >Accedi</a
                    >
                </p>
            </Card.Footer>
        </Card.Root>
    </div>
</PublicLayout>
