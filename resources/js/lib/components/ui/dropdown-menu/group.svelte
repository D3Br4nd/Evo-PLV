<script lang="ts">
  import { DropdownMenu } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<DropdownMenu.Group {...rest}>
  {@render children?.()}
</DropdownMenu.Group>


