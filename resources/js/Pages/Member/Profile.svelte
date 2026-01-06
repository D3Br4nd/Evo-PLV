<script>
    /* eslint-disable */
    import { page, router } from "@inertiajs/svelte";
    import MemberLayout from "@/layouts/MemberLayout.svelte";
    import * as Card from "@/lib/components/ui/card";
    import { Button } from "@/lib/components/ui/button";
    import * as Avatar from "@/lib/components/ui/avatar/index.js";

    let user = $derived($page.props.auth?.user);
    let isAdmin = $derived(["super_admin", "direzione", "segreteria"].includes(user?.role));

    function initials(name) {
        if (!name) return "U";
        return name
            .split(" ")
            .filter(Boolean)
            .map((n) => n[0])
            .join("")
            .toUpperCase()
            .slice(0, 2);
    }
</script>

<MemberLayout title="Profilo">
    <div class="space-y-4">
        <Card.Root>
            <Card.Content class="p-4">
                <div class="flex items-center gap-3">
                    <Avatar.Root class="size-12">
                        <Avatar.Image src={user?.avatar_url} alt={user?.name} />
                        <Avatar.Fallback class="bg-primary text-primary-foreground font-semibold">
                            {initials(user?.name)}
                        </Avatar.Fallback>
                    </Avatar.Root>
                    <div class="min-w-0">
                        <div class="font-semibold truncate">{user?.name}</div>
                        <div class="text-xs text-muted-foreground truncate">{user?.email}</div>
                    </div>
                </div>
            </Card.Content>
        </Card.Root>

        <Card.Root>
            <Card.Content class="p-4 space-y-2">
                {#if isAdmin}
                    <Button class="w-full" variant="secondary" onclick={() => router.get("/ui/admin")}>
                        Apri amministrazione
                    </Button>
                {/if}
                <Button class="w-full" variant="outline" onclick={() => router.get("/me/card")}>
                    Tessera (QR token)
                </Button>
                <Button class="w-full" variant="outline" onclick={() => router.get("/me/onboarding")}>
                    Onboarding / Notifiche / Password
                </Button>
                <Button class="w-full" variant="destructive" onclick={() => router.post("/logout")}>
                    Esci
                </Button>
            </Card.Content>
        </Card.Root>
    </div>
</MemberLayout>


