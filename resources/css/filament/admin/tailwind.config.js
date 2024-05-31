import preset from "../../../../vendor/filament/filament/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    theme: {
        extend: {
            backgroundImage: {
                "form-bg": "url('https://res.cloudinary.com/ddxwdqwkr/image/upload/q_65/v1632079580/smashing-articles/photo-1533371452382-d45a9da51ad9_1.webp')",
            },
        },
    },
};
