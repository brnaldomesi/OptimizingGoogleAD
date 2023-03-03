module.exports = {
  // important: true,
  theme: {
    inset: {
      '0': 0,
      auto: 'auto',
      '1/2': '50%',
      full: '100%'
    },

    extend: {
      spacing: {
        '72': '18rem',
        '84': '21rem',
        '96': '24rem',
      },

      colors: {
        sidemenu: {
          default: '#11142E',
          hover: '#32354d',
          text: '#CFCED3'
        },
        header: {
          default: '#303555',
          text: '#979aaa'
        },
        main: {
          dark: '#555974',
          default: '#797E94',
          light: '#9397A9',
        },
        url: {
          title: '#1A0DAB',
          site: '#006621'
        },
        formControl: {
          input: {
            default: '#EDEDF1',
            hover: '#a0a4bf'
          },
          lightgray: '#EDEDF1'
        },

        button: {
          disabled: '#CCC'
        },

        treemapColor: {
          min: '#00FF00',
          max: '#FF0000',
          background: '#edf2f7'
        },

        background: '#F7F6FC',

        dark: '#555974',
        info: '#524BCD',
        warning: '#FDA362',
        success: '#01AA85',
        danger: '#DA3D4C',
        redPrimary: '#C92C78',
        redPrimaryHover: '#96215A'
      },

      width: {
        '9/20': '45%',
        '11/20': '55%'
      },
      
      config: {
        angle: '0.6rem',
        sidebarWidth: '18rem',
        sidebarMiniWidth: '5rem',
        headerHeight: '5rem',
        logoHeight: '5.6rem',
      }
    }
  },
  variants: {
    backgroundColor: ['responsive', 'hover', 'focus'],
  },
  plugins: []
}
