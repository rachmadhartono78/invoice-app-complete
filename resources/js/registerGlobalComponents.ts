import { App } from "vue";

function toKebabCase(str: string): string {
  return str
    .replace(/([a-z])([A-Z])/g, "$1-$2")
    .replace(/[\s_]+/g, "-")
    .toLowerCase();
}

export default function registerGlobalComponents(app: App) {
  const atoms: any[] = import.meta.glob("./components/atoms/**/*.vue", { eager: true });
  // const molecules = import.meta.glob("./components/molecules/**/*.vue", { eager: true });
  const molecules: any[] = [];

  const components = { ...atoms, ...molecules };

  for (const path in components) {
    const component = (components[path] as any).default;
    if (!component) continue;

    const fileName = path.split("/").pop()?.replace(".vue", "") || "";
    const componentName = `c-${toKebabCase(fileName)}`;

    app.component(componentName, component);
  }
}