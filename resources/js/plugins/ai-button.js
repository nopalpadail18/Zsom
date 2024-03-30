import Plugin from "@ckeditor/ckeditor5-core/src/plugin";
export default class AiButton extends Plugin {
    static get pluginName() {
        return "aiButton";
    }

    init() {
        this.editor.ui.componentFactory.add("aiButton", (locale) => {
            const button = this.editor.ui.componentFactory.createButton(
                "button",
                locale
            );

            button.label = "Generate Content with AI";
            button.icon =
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.25 6.75H8.75M15.25 17.25H8.75"></path><circle cx="18.75" cy="18.75" r="1.25"></circle><circle cx="5.25" cy="18.75" r="1.25"></circle></svg>';

            button.on("execute", () => {});
        });
    }
}
