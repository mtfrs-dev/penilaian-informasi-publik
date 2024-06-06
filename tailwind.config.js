import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // 'primary'     : '#4080F4',
                'primary'     : '#2f3086',
                'primary-10'  : '#D9E6FD',
                'primary-20'  : '#BFD5FB',
                'primary-30'  : '#9FBFF9',
                'primary-40'  : '#80AAF8',
                'primary-50'  : '#6095F6',
                // 'primary-60'  : '#4080F4',
                'primary-60'  : '#2f3086',
                'primary-70'  : '#356BCB',
                'primary-80'  : '#2B55A3',
                'primary-90'  : '#20407A',
                'primary-100' : '#152B51',
                'primary-110' : '#0D1A31',
                
                'success'     : '#76C41D',
                'success-10'  : '#E4F3D2',
                'success-20'  : '#D1EBB4',
                'success-30'  : '#BAE18E',
                'success-40'  : '#A4D868',
                'success-50'  : '#8DCE43',
                'success-60'  : '#76C41D',
                'success-70'  : '#62A318',
                'success-80'  : '#4F8313',
                'success-90'  : '#3B620E',
                'success-100' : '#27410A',
                'success-110' : '#182706',
                
                'warning'     : '#FDBE00',
                'warning-10'  : '#FFF2CC',
                'warning-20'  : '#FEE9AA',
                'warning-30'  : '#FEDE80',
                'warning-40'  : '#FED455',
                'warning-50'  : '#FDC92A',
                'warning-60'  : '#FDBE00',
                'warning-70'  : '#D39E00',
                'warning-80'  : '#A97F00',
                'warning-90'  : '#7E5F00',
                'warning-100' : '#543F00',
                'warning-110' : '#332600',
                
                'danger'     : '#FF4248',
                'danger-10'  : '#FFD9DA',
                'danger-20'  : '#FFC0C2',
                'danger-30'  : '#FFA0A3',
                'danger-40'  : '#FF8185',
                'danger-50'  : '#FF6166',
                'danger-60'  : '#FF4248',
                'danger-70'  : '#D4373C',
                'danger-80'  : '#AA2C30',
                'danger-90'  : '#802124',
                'danger-100' : '#551618',
                'danger-110' : '#330D0E',
            },
        },
    },

    plugins: [forms],
};
