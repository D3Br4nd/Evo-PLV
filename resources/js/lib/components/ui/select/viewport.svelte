<script lang="ts">
  import { Select } from "bits-ui";
  let { children, ...rest } = $props<any>();
</script>

<Select.Viewport {...rest}>
  {@render children?.()}
</Select.Viewport>


