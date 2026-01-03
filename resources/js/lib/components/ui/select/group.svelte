<script lang="ts">
  import { Select } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<Select.Group {...rest}>
  {@render children?.()}
</Select.Group>


