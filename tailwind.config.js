export default {
    theme: {
        extend: {
        scrollBehavior: {
            smooth: 'smooth',
                fontFamily: {
                    outfit: ["Outfit", "sans-serif"],
            },
        },
    },
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
};
