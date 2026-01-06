<script>
    import AdminLayout from "@/layouts/AdminLayout.svelte";
    import { router, page } from "@inertiajs/svelte";
    import { Input } from "@/lib/components/ui/input";
    import { Button } from "@/lib/components/ui/button";
    import * as Card from "@/lib/components/ui/card";
    import * as Avatar from "@/lib/components/ui/avatar/index.js";

    let { flash } = $props();
    let user = $derived($page.props.auth?.user);

    let profileProcessing = $state(false);
    let avatarProcessing = $state(false);
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

    let avatarFile = $state(null);
    let avatarPreviewUrl = $state(null);
    // Keep last object URL in a non-reactive variable to avoid $effect loops.
    let lastAvatarObjectUrl = null;

    $effect(() => {
        // Depend ONLY on avatarFile to prevent infinite reruns.
        if (lastAvatarObjectUrl) {
            URL.revokeObjectURL(lastAvatarObjectUrl);
            lastAvatarObjectUrl = null;
        }

        if (!avatarFile) {
            avatarPreviewUrl = null;
            return;
        }

        lastAvatarObjectUrl = URL.createObjectURL(avatarFile);
        avatarPreviewUrl = lastAvatarObjectUrl;

        return () => {
            if (lastAvatarObjectUrl) {
                URL.revokeObjectURL(lastAvatarObjectUrl);
                lastAvatarObjectUrl = null;
            }
        };
    });

    function uploadAvatar() {
        if (!avatarFile) return;
        avatarProcessing = true;
        const fd = new FormData();
        fd.append("avatar", avatarFile);
        router.post("/admin/profile/avatar", fd, {
            preserveScroll: true,
            onFinish: () => {
                avatarProcessing = false;
                avatarFile = null;
            },
        });
    }

    function removeAvatar() {
        if (!user?.avatar_url) return;
        if (!confirm("Rimuovere l’avatar?")) return;
        avatarProcessing = true;
        router.delete("/admin/profile/avatar", {
            preserveScroll: true,
            onFinish: () => {
                avatarProcessing = false;
                avatarFile = null;
            },
        });
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
                        <Avatar.Root class="size-10">
                            <Avatar.Image src={user?.avatar_url} alt={user?.name} />
                            <Avatar.Fallback class="bg-primary text-primary-foreground text-sm font-semibold">
                                {initials(user?.name)}
                            </Avatar.Fallback>
                        </Avatar.Root>
                        <div class="min-w-0">
                            <div class="font-medium truncate">{user?.name}</div>
                            <div class="text-xs text-muted-foreground truncate">
                                {user?.role}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-medium" for="p-avatar">Avatar</label>
                        <div class="flex items-center gap-2">
                            <Input
                                id="p-avatar"
                                type="file"
                                accept="image/*"
                                disabled={avatarProcessing}
                                onchange={(e) => (avatarFile = e.currentTarget.files?.[0] || null)}
                            />
                            <Button variant="outline" disabled={avatarProcessing || !avatarFile} onclick={uploadAvatar}>
                                {avatarProcessing ? "Caricamento..." : "Carica"}
                            </Button>
                            <Button
                                variant="destructive"
                                disabled={avatarProcessing || !user?.avatar_url}
                                onclick={removeAvatar}
                            >
                                Rimuovi
                            </Button>
                        </div>
                        {#if $page.props.errors?.avatar}
                            <p class="text-sm text-destructive">{$page.props.errors.avatar}</p>
                        {/if}
                        <p class="text-xs text-muted-foreground">
                            JPG/PNG/WebP, max 2MB.
                        </p>
                    </div>

                    {#if avatarPreviewUrl || user?.avatar_url}
                        <div class="space-y-2">
                            <div class="text-sm font-medium">Anteprima</div>
                            <div class="rounded-lg border bg-card p-4">
                                <div class="flex items-center gap-4">
                                    <img
                                        src={avatarPreviewUrl || user?.avatar_url}
                                        alt="Anteprima avatar"
                                        class="h-24 w-24 rounded-md object-cover border bg-background"
                                    />
                                    <div class="text-xs text-muted-foreground">
                                        {#if avatarPreviewUrl}
                                            Questa è l’immagine selezionata (prima del caricamento).
                                        {:else}
                                            Avatar attuale.
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}

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


