const root = document.documentElement
const theme = localStorage.getItem('theme')
const attr = 'data-bs-theme'
if (theme) root.setAttribute(attr, theme)

document.getElementById('btnSwitch').addEventListener('click', () => {
    if (root.getAttribute(attr) === 'dark') {
        root.setAttribute(attr, 'light')
        localStorage.setItem('theme', 'light')
    } else {
        root.setAttribute(attr, 'dark')
        localStorage.setItem('theme', 'dark')
    }
})