(function (blocks, editor, components, i18n, element) {
    var __ = i18n.__
    var el = element.createElement
    var registerBlockType = blocks.registerBlockType
    var RichText = editor.RichText
    var BlockControls = editor.BlockControls
    var MediaUpload = editor.MediaUpload
    var InspectorControls = editor.InspectorControls
    var PanelBody = components.PanelBody
    var TextControl = components.TextControl
    var ToggleControl = components.ToggleControl
  
    registerBlockType('astrometry/photodata', { 
      title: 'Astrometry', 
      description: __('A custom block for displaying an astrometry image.'), 
      icon: 'art', 
      category: 'common', 
      supports: {
        align: true,
        alignWide: true
      },
      attributes: {
        title: {
          type: 'array',
          source: 'children',
          selector: '.data1'
        },
        subtitle: {
          type: 'array',
          source: 'children',
          selector: '.data2'
        },
        bio: {
          type: 'array',
          source: 'children',
          selector: '.data3'
        },
        mediaID: {
          type: 'number'
        },
        mediaURL: {
          type: 'string',
          source: 'attribute',
          selector: 'img',
          attribute: 'src'
        },
        showAstrometryMetaData: {
          type: 'boolean'
        },
        showAstrometrySkyplot: {
          type: 'boolean'
        }
      },
  
      edit: function (props) {
        var attributes = props.attributes
  
        var onSelectImage = function (media) {
          return props.setAttributes({
            mediaURL: media.url,
            mediaID: media.id
          })
        }
  
        return [
          //Controls
          el(BlockControls, { key: 'controls' },
            el('div', { className: 'components-toolbar' },
              el(MediaUpload, {
                onSelect: onSelectImage,
                type: 'image',
                render: function (obj) {
                  return el(components.Button, {
                    className: 'components-icon-button components-toolbar__control',
                    onClick: obj.open
                  },
                  el('svg', { className: 'dashicon dashicons-edit', width: '20', height: '20' },
                    el('path', { d: 'M2.25 1h15.5c.69 0 1.25.56 1.25 1.25v15.5c0 .69-.56 1.25-1.25 1.25H2.25C1.56 19 1 18.44 1 17.75V2.25C1 1.56 1.56 1 2.25 1zM17 17V3H3v14h14zM10 6c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2zm3 5s0-6 3-6v10c0 .55-.45 1-1 1H5c-.55 0-1-.45-1-1V8c2 0 3 4 3 4s1-3 3-3 3 2 3 2z' })
                  ))
                }
              })
            )
          ),

          //Inspector
          el(InspectorControls, { key: 'inspector' }, 
            el(PanelBody, {
              title: __('Settings'),
              className: 'block-settings',
              initialOpen: true
            },
            el(ToggleControl, {
              label: __('Show Astrometry Meta'),
              checked: attributes.showAstrometryMetaData,
              onChange: function (showMeta) {
                props.setAttributes( {  showAstrometryMetaData: !attributes.showAstrometryMetaData } )
              }
            }),			   
			el(ToggleControl, {
              label: __('Show Skyplot'),
              checked: attributes.showAstrometrySkyplot,
              onChange: function (showPlot) {
                props.setAttributes( {  showAstrometrySkyplot: !attributes.showAstrometrySkyplot } )
              }
            }))
          ),
          //WP Block
          el('div', { className: props.className },

            el('div', {
              className: attributes.mediaID ? 'astrometry-image image-active' : 'astrometry-image image-inactive',
              style: {}
                },
                el(MediaUpload, {
                onSelect: onSelectImage,
                type: 'image',
                value: attributes.mediaID,
                render: function (obj) {

                    return (
                        el('div',{},
                            el('img', { src: attributes.mediaURL }),
                            el(components.Button, {
                                className: attributes.mediaID ? 'button button-large' : 'button button-large',
                                style: {'position':'absolute', 'right':'5px', 'bottom':'5px'},
                                onClick: obj.open
                                }, __('Upload Image'))
                        )
                    )
                    /*
                    return el(components.Button, {
                    className: attributes.mediaID ? 'image-button' : 'button button-large',
                    onClick: obj.open
                    },
                    !attributes.mediaID ? __('Upload Image') : el('img', { src: attributes.mediaURL })
                    )
                    */
                }
                })
            ),

            el('div', { className: 'astrometry-image-data' },
            
            	attributes.showAstrometrySkyplot && el('div', { class:'skyplot'}, 
					el('div', {},"")
				),

			   el('div', { className: 'astrometry-data' },
			   
                el('label', {}, __('Aufnahmedatum')),
                el(RichText, {
                    key: 'editable',
                    tagName: 'p',
                    className : 'data1',
                    placeholder: __('Aufnahmedatum'),
                    keepPlaceholderOnFocus: true,
                    value: attributes.title,
                    onChange: function (newTitle) {
                    props.setAttributes({ title: newTitle })
                    }
                }),
                el('label', {}, __('Belichtung') ),
                el(RichText, {
                    tagName: 'p',
                    className : 'data2',
                    placeholder: __('Belichtung'),
                    keepPlaceholderOnFocus: true,
                    value: attributes.subtitle,
                    onChange: function (newSubtitle) {
                    props.setAttributes({ subtitle: newSubtitle })
                    }
                }),
                el('label', {}, __('Ausrüstung') ),
                el(RichText, {
                    tagName: 'p',
                    className : 'data3',
                    placeholder: __('Ausrüstung'),
                    keepPlaceholderOnFocus: true,
                    value: attributes.bio,
                    onChange: function (newBio) {
                    props.setAttributes({ bio: newBio })
                    }
                }),

                attributes.showAstrometryMetaData && el('label', {}, "RA" ),
                attributes.showAstrometryMetaData && el('p', {}, "{RA}"),
                attributes.showAstrometryMetaData && el('label', {}, "DEC" ),
                attributes.showAstrometryMetaData && el('p', {}, "{DEC}"),
                attributes.showAstrometryMetaData && el('label', {}, "Job" ),
                attributes.showAstrometryMetaData && el('p', {}, "{JOB}"),
                attributes.showAstrometryMetaData && el('label', {}, "Objekte" ),
                attributes.showAstrometryMetaData && el('p', {}, "{OBJECTS}")
        				)
              )
          )
        ]
      },
  
      save: function (props) {
        var attributes = props.attributes
        var imageClass = 'wp-image-' + props.attributes.mediaID

        return (
            el('div', { className: props.className },

                attributes.mediaURL && el('div', { className: 'astrometry-image', 'data-mediaid':props.attributes.mediaID },
                    el('figure', { class: imageClass },
                        el('img', { src: attributes.mediaURL, class: '{solvingState}', 'data-solved': '{solvingData}' })
                    )
                ),            

                el('div', { className: 'astrometry-image-data', style: { } },

				   attributes.showAstrometrySkyplot && el('div', { class: 'skyplot'}, 
					el('div', { }, "{SKYPLOT}"),									 
					),
				   
				   el('div', { className: 'astrometry-data' },
				   
                    attributes.title != "" && el('label', {}, __('Aufnahmedatum')),
                    attributes.title != "" && el(RichText.Content, {
                        tagName: 'p',
                        className : 'data1',
                        value: attributes.title
                    }),
                    attributes.subtitle != "" && el('label', {}, __('Belichtung') ),
                    attributes.subtitle != "" && el(RichText.Content, {
                        tagName: 'p',
                        className : 'data2',
                        value: attributes.subtitle
                    }),
                    attributes.bio != "" && el('label', {}, __('Ausrüstung') ),
                    attributes.bio != "" && el(RichText.Content, {
                        tagName: 'p',
                        className : 'data3',
                        value: attributes.bio
                    }),

                    attributes.showAstrometryMetaData && el('label', {}, "RA" ),
                    attributes.showAstrometryMetaData && el('p', {}, "{RA}"),
                    attributes.showAstrometryMetaData && el('label', {}, "DEC" ),
                    attributes.showAstrometryMetaData && el('p', {}, "{DEC}"),
                    attributes.showAstrometryMetaData && el('label', {}, "Job" ),
                    attributes.showAstrometryMetaData && el('p', {}, "{JOB}"),
                    attributes.showAstrometryMetaData && el('label', {}, "Objekte" ),
                    attributes.showAstrometryMetaData && el('p', {}, "{OBJECTS}")
					  
					  )

                )
            )
          )
        
      }
    })
  })(
    window.wp.blocks,
    window.wp.editor,
    window.wp.components,
    window.wp.i18n,
    window.wp.element
  )