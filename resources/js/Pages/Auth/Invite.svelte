<script>
    /* eslint-disable */
    import { useForm } from "@inertiajs/svelte";
    import PublicLayout from "@/layouts/PublicLayout.svelte";
    import { Button } from "@/lib/components/ui/button";
    import { Input } from "@/lib/components/ui/input";
    import * as Card from "@/lib/components/ui/card";

    let { token, email, name } = $props();

    const form = useForm({
        password: "",
        password_confirmation: "",
    });

    function submit(e) {
        e.preventDefault();
        $form.post(`/invite/${token}`);
    }
</script>

<PublicLayout title="Invito socio">
    <div class="mx-auto w-full max-w-md">
        <Card.Root>
            <Card.Header class="text-center">
                <Card.Title>Completa il tuo accesso</Card.Title>
                <Card.Description>
                    Ciao{name ? ` ${name}` : ""}, imposta una password per l’account:
                    <span class="font-mono">{email}</span>
                </Card.Description>
            </Card.Header>
            <Card.Content>
                <form onsubmit={submit} class="space-y-4">
                    <div class="space-y-1.5">
                        <label for="password" class="text-sm font-medium">Nuova password</label>
                        <Input
                            id="password"
                            type="password"
                            bind:value={$form.password}
                            required
                            placeholder="Minimo 8 caratteri"
                        />
                        {#if $form.errors.password}
                            <p class="text-sm text-destructive">{$form.errors.password}</p>
                        {/if}
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="text-sm font-medium"
                            >Conferma password</label
                        >
                        <Input
                            id="password_confirmation"
                            type="password"
                            bind:value={$form.password_confirmation}
                            required
                            placeholder="Ripeti la password"
                        />
                        {#if $form.errors.password_confirmation}
                            <p class="text-sm text-destructive">
                                {$form.errors.password_confirmation}
                            </p>
                        {/if}
                    </div>

                    <Button type="submit" disabled={$form.processing} class="w-full">
                        {$form.processing ? "Salvataggio..." : "Imposta password e continua"}
                    </Button>
                </form>
            </Card.Content>
        </Card.Root>
    </div>
</PublicLayout>


