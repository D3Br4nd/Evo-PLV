<script>
	import * as Sidebar from "@/lib/components/ui/sidebar/index.js";
	import { Link } from "@inertiajs/svelte";
	let {
		items,
		...restProps
	} = $props();
</script>

<Sidebar.Group {...restProps}>
	<Sidebar.GroupContent>
		<Sidebar.Menu>
			{#each items as item (item.title)}
				<Sidebar.MenuItem>
					<Sidebar.MenuButton>
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