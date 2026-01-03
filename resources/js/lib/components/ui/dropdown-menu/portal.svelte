<script lang="ts">
  import { DropdownMenu } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<DropdownMenu.Portal {...rest}>
  {@render children?.()}
</DropdownMenu.Portal>


