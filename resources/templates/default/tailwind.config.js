import aspectRatio from '@tailwindcss/aspect-ratio';
import typography from '@tailwindcss/typography';
import plugin from 'tailwindcss/plugin';

export default {
   content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './scms/templates/**/*.blade.php',
   ],
   theme: {
      screens: {
         'xs': '430px',
         's': '576px',
         'sm': '640px',
         'md': '768px',
         'm': '991px',
         'lg': '1024px',
         'xl': '1280px',
         '2xl': '1536px',
      },
   },
   plugins: [
      typography({
         className: 'description',
      }),
      aspectRatio,
      plugin(function ({ addBase, matchUtilities, theme }) {
         addBase({
            'input::-webkit-outer-spin-button, input::-webkit-inner-spin-button': {
               '-webkit-appearance': 'none',
               'margin': '0',
            },
            'input[type=number]': {
               '-moz-appearance': 'textfield',
            },
            '.container': {
               'max-width': '86.25rem',
               'padding': '0 1.25rem',
               'margin': '0 auto',
            },
         });
         matchUtilities(
            {
               'text-shadow': (value) => ({
                  textShadow: value,
               }),
            },
            { values: theme('textShadow') },
         )
      }),
   ],
}
