<script lang="ts">
  import { Select } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<Select.Root {...rest}>
  {@render children?.()}
</Select.Root>


