<script>
	/* eslint-disable */
	import * as Sidebar from "@/lib/components/ui/sidebar/index.js";
	import SiteHeader from "@/lib/components/site-header.svelte";
	import MemberSidebar from "@/lib/components/member-sidebar.svelte";

	let { title = "Socio", children, headerActions } = $props();
</script>

<svelte:head>
	<title>{title} · Pro Loco</title>
</svelte:head>

<Sidebar.Provider
	style="--sidebar-width: calc(var(--spacing) * 72); --header-height: calc(var(--spacing) * 12);"
>
	<MemberSidebar variant="inset" />
	<Sidebar.Inset>
		<SiteHeader {title} {headerActions} />
		<div class="flex flex-1 flex-col">
			<div class="@container/main flex flex-1 flex-col gap-2">
				<div class="flex flex-col gap-4 py-4 md:gap-6 md:py-6">
					<div class="px-4 lg:px-6">
						{@render children()}
					</div>
				</div>
			</div>
		</div>
	</Sidebar.Inset>
</Sidebar.Provider>


