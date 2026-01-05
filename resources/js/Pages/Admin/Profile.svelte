<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router, page } from "@inertiajs/svelte";
    import { Input } from "@/lib/components/ui/input";
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";

    let { flash } = $props();
    let user = $derived($page.props.auth?.user);

    let profileProcessing = $state(false);
    let passwordProcessing = $state(false);

    let profileForm = $state({ name: "", email: "" });
    let hydrated = $state(false);

    // Hydrate form once from the authenticated user.
    $effect(() => {
        if (!hydrated && user) {
            profileForm = { name: user.name || "", email: user.email || "" };
            hydrated = true;
        }
    });

    let passwordForm = $state({
        current_password: "",
        password: "",
        password_confirmation: "",
    });

    function initials(name) {
        if (!name) return "U";
        return name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase()
            .slice(0, 2);
    }

    function saveProfile() {
        profileProcessing = true;
        router.patch("/admin/profile", profileForm, {
            preserveScroll: true,
            onFinish: () => (profileProcessing = false),
        });
    }

    function changePassword() {
        passwordProcessing = true;
        router.put("/admin/profile/password", passwordForm, {
            preserveScroll: true,
            onSuccess: () => {
                passwordForm = {
                    current_password: "",
                    password: "",
                    password_confirmation: "",
                };
            },
            onFinish: () => (passwordProcessing = false),
        });
    }
</script>

<AdminLayout title="Profilo">
    <div class="space-y-6">
        <p class="text-sm text-muted-foreground">Dati personali e cambio password.</p>

        {#if flash?.success}
            <div class="text-sm text-green-600 dark:text-green-400">
                {flash.success}
            </div>
        {/if}
        {#if flash?.error}
            <div class="text-sm text-destructive">{flash.error}</div>
        {/if}

        <div class="grid gap-6 lg:grid-cols-2">
            <Card.Root>
                <Card.Header>
                    <Card.Title>Dati profilo</Card.Title>
                    <Card.Description>Aggiorna nome ed email.</Card.Description>
                </Card.Header>
                <Card.Content class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-foreground text-sm font-semibold"
                        >
                            {initials(user?.name)}
                        </div>
                        <div class="min-w-0">
                            <div class="font-medium truncate">{user?.name}</div>
                            <div class="text-xs text-muted-foreground truncate">
                                {user?.role}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium" for="p-name">Nome</label>
                        <Input id="p-name" bind:value={profileForm.name} />
                        {#if $page.props.errors?.name}
                            <p class="text-sm text-destructive">{$page.props.errors.name}</p>
                        {/if}
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium" for="p-email">Email</label>
                        <Input id="p-email" type="email" bind:value={profileForm.email} />
                        {#if $page.props.errors?.email}
                            <p class="text-sm text-destructive">{$page.props.errors.email}</p>
                        {/if}
                    </div>
                </Card.Content>
                <Card.Footer class="justify-end">
                    <Button onclick={saveProfile} disabled={profileProcessing}>
                        {profileProcessing ? "Salvataggio..." : "Salva"}
                    </Button>
                </Card.Footer>
            </Card.Root>

            <Card.Root>
                <Card.Header>
                    <Card.Title>Cambio password</Card.Title>
                    <Card.Description>
                        Inserisci la password attuale e quella nuova.
                    </Card.Description>
                </Card.Header>
                <Card.Content class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium" for="pw-current"
                            >Password attuale</label
                        >
                        <Input
                            id="pw-current"
                            type="password"
                            bind:value={passwordForm.current_password}
                        />
                        {#if $page.props.errors?.current_password}
                            <p class="text-sm text-destructive">
                                {$page.props.errors.current_password}
                            </p>
                        {/if}
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium" for="pw-new"
                            >Nuova password</label
                        >
                        <Input id="pw-new" type="password" bind:value={passwordForm.password} />
                        {#if $page.props.errors?.password}
                            <p class="text-sm text-destructive">{$page.props.errors.password}</p>
                        {/if}
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium" for="pw-confirm"
                            >Conferma password</label
                        >
                        <Input
                            id="pw-confirm"
                            type="password"
                            bind:value={passwordForm.password_confirmation}
                        />
                    </div>
                </Card.Content>
                <Card.Footer class="justify-end">
                    <Button
                        onclick={changePassword}
                        disabled={passwordProcessing}
                        variant="outline"
                    >
                        {passwordProcessing ? "Aggiornamento..." : "Aggiorna password"}
                    </Button>
                </Card.Footer>
            </Card.Root>
        </div>
    </div>
</AdminLayout>


