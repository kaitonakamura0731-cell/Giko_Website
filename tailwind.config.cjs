/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './src/**/*.html',
    './src/**/*.php',
    './src/**/*.js',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: '#0055FF',
        secondary: '#111111',
        accent: '#222222',
        text: '#FFFFFF',
        textLight: '#AAAAAA',
        dark: '#000000',
      },
      fontFamily: {
        sans: ['"Noto Sans JP"', 'sans-serif'],
        en: ['"Montserrat"', 'sans-serif'],
      },
      spacing: {
        '128': '32rem',
      }
    }
  },
  safelist: [
    // Admin options builder (JS template literals)
    'grid-cols-12', 'grid-cols-1',
    'col-span-1', 'col-span-3', 'col-span-5',
    'md:col-span-2', 'md:grid-cols-2', 'md:grid-cols-3', 'md:grid-cols-4', 'md:grid-cols-6',
    'md:w-auto',
    'bg-black/20', 'bg-black/40', 'bg-black/50', 'bg-black/60', 'bg-black/70',
    'bg-gray-800/50', 'bg-gray-900/90',
    'bg-green-900', 'bg-green-900/50', 'bg-green-900/80',
    'bg-red-500/10', 'bg-red-900/50',
    'bg-white/5',
    'border-green-400', 'border-green-500', 'border-green-700', 'border-green-700/50',
    'border-red-400', 'border-red-500/50', 'border-red-700',
    'border-blue-700',
    'border-white/5', 'border-white/10',
    'text-blue-300', 'text-blue-400',
    'text-green-100', 'text-green-300',
    'text-red-100', 'text-red-300',
    'text-[10px]',
    'focus:border-primary',
    'hover:bg-yellow-500', 'hover:text-yellow-300',
    'inset-y-0', 'right-2', 'z-40',
    'max-w-sm',
    'h-32', 'h-40', 'w-28',
    'p-0', 'p-1', 'pl-10', 'pr-3', 'py-0.5',
    'space-y-10',
    'backdrop-blur-sm',
    'divide-y', 'divide-gray-700',
    'rounded-xl', 'rounded-bl',
    'font-mono',
    'sm:grid-cols-3',
    'cursor-grab', 'cursor-default',
    // File input pseudo-element
    'file:py-0', 'file:px-2', 'file:rounded', 'file:bg-gray-700', 'file:text-gray-200',
  ],
  plugins: [],
}
