<script>
	/* eslint-disable */
	import InnerShadowTopIcon from "@tabler/icons-svelte/icons/inner-shadow-top";
	import CreditCardIcon from "@tabler/icons-svelte/icons/credit-card";
	import NotificationIcon from "@tabler/icons-svelte/icons/notification";
	import LogoutIcon from "@tabler/icons-svelte/icons/logout";
	import { Link, router, page } from "@inertiajs/svelte";
	import * as Sidebar from "@/lib/components/ui/sidebar/index.js";

	let { ...restProps } = $props();
	let user = $derived($page.props.auth?.user);

	const nav = [
		{ title: "Tessera", url: "/me/card", icon: CreditCardIcon },
		{ title: "Onboarding", url: "/me/onboarding", icon: NotificationIcon },
	];
</script>

<Sidebar.Root collapsible="offcanvas" {...restProps}>
	<Sidebar.Header>
		<Sidebar.Menu>
			<Sidebar.MenuItem>
				<Sidebar.MenuButton class="data-[slot=sidebar-menu-button]:!p-1.5">
					{#snippet child({ props })}
						<Link href="/me/card" {...props}>
							<InnerShadowTopIcon class="!size-5" />
							<span class="text-base font-semibold">Pro Loco</span>
						</Link>
					{/snippet}
				</Sidebar.MenuButton>
			</Sidebar.MenuItem>
		</Sidebar.Menu>
	</Sidebar.Header>

	<Sidebar.Content>
		<Sidebar.Group>
			<Sidebar.GroupLabel>Socio</Sidebar.GroupLabel>
			<Sidebar.GroupContent>
				<Sidebar.Menu>
					{#each nav as item (item.title)}
						<Sidebar.MenuItem>
							<Sidebar.MenuButton tooltipContent={item.title}>
								{#snippet child({ props })}
									<Link href={item.url} {...props}>
										<item.icon />
										<span>{item.title}</span>
									</Link>
								{/snippet}
							</Sidebar.MenuButton>
						</Sidebar.MenuItem>
					{/each}
				</Sidebar.Menu>
			</Sidebar.GroupContent>
		</Sidebar.Group>
	</Sidebar.Content>

	<Sidebar.Footer>
		<Sidebar.Menu>
			<Sidebar.MenuItem>
				<Sidebar.MenuButton>
					{#snippet child({ props })}
						<button
							{...props}
							type="button"
							class="w-full"
							onclick={() => router.post("/logout")}
						>
							<LogoutIcon />
							<span>Esci</span>
						</button>
					{/snippet}
				</Sidebar.MenuButton>
			</Sidebar.MenuItem>
		</Sidebar.Menu>

		{#if user}
			<div class="px-2 pt-2 text-xs text-muted-foreground">
				<div class="truncate">{user.name}</div>
				<div class="truncate">{user.email}</div>
			</div>
		{/if}
	</Sidebar.Footer>
</Sidebar.Root>


