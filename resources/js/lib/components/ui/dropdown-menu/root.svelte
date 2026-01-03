<script lang="ts">
  import { DropdownMenu } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<DropdownMenu.Root {...rest}>
  {@render children?.()}
</DropdownMenu.Root>


