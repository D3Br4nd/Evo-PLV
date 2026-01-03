<script lang="ts">
  import { cva, type VariantProps } from "class-variance-authority";
  import { cn } from "@/lib/utils/cn";

  export const buttonVariants = cva(
    "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0",
    {
      variants: {
        variant: {
          default: "bg-primary text-primary-foreground hover:bg-primary/90",
          secondary: "bg-secondary text-secondary-foreground hover:bg-secondary/80",
          outline:
            "border border-input bg-background hover:bg-accent hover:text-accent-foreground",
          ghost: "hover:bg-accent hover:text-accent-foreground",
          destructive:
            "bg-destructive text-destructive-foreground hover:bg-destructive/90",
          link: "text-primary underline-offset-4 hover:underline",
        },
        size: {
          default: "h-10 px-4 py-2",
          sm: "h-9 rounded-md px-3",
          lg: "h-11 rounded-md px-8",
          icon: "h-10 w-10",
          "icon-sm": "h-9 w-9",
          "icon-lg": "h-11 w-11",
        },
      },
      defaultVariants: {
        variant: "default",
        size: "default",
      },
    },
  );

  type Props = VariantProps<typeof buttonVariants> & {
    class?: string;
    href?: string;
    type?: "button" | "submit" | "reset";
    disabled?: boolean;
    children?: any;
    // Allow passing arbitrary attributes/events (data-*, aria-*, onclick, etc.)
    [key: string]: any;
  };

  let {
    variant,
    size,
    class: className,
    href,
    type = "button",
    disabled,
    children,
    ...rest
  } = $props() as Props;
</script>

{#if href}
  <a
    class={cn(buttonVariants({ variant, size }), className)}
    href={href}
    {...rest}
  >
    {@render children?.()}
  </a>
{:else}
  <button
    class={cn(buttonVariants({ variant, size }), className)}
    type={type}
    disabled={disabled}
    {...rest}
  >
    {@render children?.()}
  </button>
{/if}


