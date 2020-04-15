(function (blocks, blockEditor, components, i18n, element) {
    var __ = i18n.__
    var el = element.createElement
    var registerBlockType = blocks.registerBlockType
    var RichText = blockEditor.RichText
    var BlockControls = blockEditor.BlockControls
    var MediaUpload = blockEditor.MediaUpload
    var InspectorControls = blockEditor.InspectorControls
    var PanelBody = components.PanelBody
    var TextControl = components.TextControl
    var ToggleControl = components.ToggleControl
	  
    registerBlockType('astrometry/photodata', { 
      title: __('Astrometry', 'astrometry'), 
      description: __('A custom block for displaying an astrometry image', 'astrometry'), 
      icon: 'art', 
      category: 'common', 
      supports: {
        align: true,
        alignWide: true
      },
      attributes: {
        date: {
          type: 'array',
          source: 'children',
          selector: '.date'
        },
        framesCount: {
          type: 'array',
          source: 'children',
          selector: '.framesCount'
        },
        framesSeconds: {
          type: 'array',
          source: 'children',
          selector: '.framesSeconds'
        },
        equipment: {
          type: 'array',
          source: 'children',
          selector: '.equipment'
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
        },
        showHdCatalogue: {
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
		
		var formatExposureTime = function(seconds) {
			var minutes = Math.floor(seconds / 60);
			var hours =  Math.floor(minutes / 60);

			if(hours > 0)
			{
				return hours + "." + Math.floor(60/minutes) + "h";
			}
			if(minutes > 0)
			{
				return minutes + "." + Math.floor(60/seconds) + "min";
			}
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
              title: __('Settings', 'astrometry'),
              className: 'block-settings',
              initialOpen: true
            },
            el(ToggleControl, {
              label: __('Show Astrometry Meta', 'astrometry'),
              checked: attributes.showAstrometryMetaData,
              onChange: function (showMeta) {
                props.setAttributes( {  showAstrometryMetaData: !attributes.showAstrometryMetaData } )
              }
            }),			   
			el(ToggleControl, {
              label: __('Show Skyplot', 'astrometry'),
              checked: attributes.showAstrometrySkyplot,
              onChange: function (showPlot) {
                props.setAttributes( {  showAstrometrySkyplot: !attributes.showAstrometrySkyplot } )
              }
            }),
            el(ToggleControl, {
              label: __('Show HD Catalogue Stars', 'astrometry'),
              checked: attributes.showHdCatalogue,
              onChange: function (showHd) {
                props.setAttributes( {  showHdCatalogue: !attributes.showHdCatalogue } )
              }
            })
            )
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
                                }, __('Upload Image', 'astrometry'))
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
			   
                el('label', {}, __('Date', 'astrometry')),
                el(RichText, {
                    key: 'editable',
                    tagName: 'p',
                    className : 'date',
                    placeholder: __('Date', 'astrometry'),
                    keepPlaceholderOnFocus: true,
                    value: attributes.date,
                    onChange: function (newVal) {
                    	props.setAttributes({ date: newVal })
                    }
                }),
                el('label', {}, __('Frames', 'astrometry') ),
				el('p', { className: 'frames' },				   
				  	el(RichText, {
					  tagName: 'span',
					  className : 'framesCount',
					  placeholder: __('Number', 'astrometry'),
					  keepPlaceholderOnFocus: true,
					  value: attributes.framesCount,
					  onChange: function (newVal) {
						  props.setAttributes({ framesCount: newVal })
					  }
				  	}),
				   	el('span', { className: 'framesX' }, 'x'),
					el(RichText, {
					  tagName: 'span',
					  className : 'framesSeconds',
					  placeholder: __('Exposure time', 'astrometry'),
					  keepPlaceholderOnFocus: true,
					  value: attributes.framesSeconds,
					  onChange: function (newVal) {
						  props.setAttributes({ framesSeconds: newVal })
					  }			  
                	}),
					el('span', { className: 'framesSecondsSymbol' },"''"),
				   (attributes.framesSeconds*attributes.framesCount)>0 && el('span', { className: 'framesTotalCon' }, 
						el('span', { className: 'label' }, __('Total exposure', 'astrometry')),
						el('span', { className: 'framesTotal' }, formatExposureTime(attributes.framesSeconds*attributes.framesCount))
					)				   
				),
                el('label', {}, __('Equipment', 'astrometry') ),
                el(RichText, {
                    tagName: 'p',
                    className : 'equipment',
                    placeholder: __('Equipment', 'astrometry'),
                    keepPlaceholderOnFocus: true,
                    value: attributes.equipment,
                    onChange: function (newVal) {
                    	props.setAttributes({ equipment: newVal })
                    }
                }),

                attributes.showAstrometryMetaData && el('label', {}, "RA" ),
                attributes.showAstrometryMetaData && el('p', {}, "..."),
                attributes.showAstrometryMetaData && el('label', {}, "DEC" ),
                attributes.showAstrometryMetaData && el('p', {}, "..."),
                attributes.showAstrometryMetaData && el('label', {}, __('Job', 'astrometry') ),
                attributes.showAstrometryMetaData && el('p', {}, "..."),
                attributes.showAstrometryMetaData && el('label', {}, __('Objects', 'astrometry') ),
                attributes.showAstrometryMetaData && el('p', {}, "...")
        				)
              )
          )
        ]
      },
  
      save: function (props) {
        var attributes = props.attributes
        var imageClass = 'wp-image-' + props.attributes.mediaID
		var formatExposureTime = function(seconds) {
			var minutes = Math.floor(seconds / 60);
			var hours =  Math.floor(minutes / 60);

			if(hours > 0)
			{
				return hours + "." + Math.floor(60/minutes) + "h";
			}
			if(minutes > 0)
			{
				return minutes + "." + Math.floor(60/seconds) + "min";
			}
		}
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
				   
                    attributes.date != "" && el('label', {}, __('Date', 'astrometry')),
                    attributes.date != "" && el(RichText.Content, {
                        tagName: 'p',
                        className : 'date',
                        value: attributes.date
                    }),
                    attributes.framesCount != "" && el('label', {}, __('Frames', 'astrometry') ), 
					attributes.framesCount != "" && el('p', { className: 'frames' },
						attributes.framesCount != "" && el(RichText.Content, {
							tagName: 'span',
							className : 'framesCount',
							value: attributes.framesCount
						}),
						el('span', { className: 'framesX'}, 'x'),
						attributes.framesSeconds != "" && el(RichText.Content, {
							tagName: 'span',
							className : 'framesSeconds',
							value: attributes.framesSeconds
						}),
						el('span', { className: 'framesSecondsSymbol' },"''"),
				   		(attributes.framesSeconds*attributes.framesCount)>0 && el('span', { className: 'framesTotalCon' }, 
							el('span', { className: 'label' }, __('Total exposure', 'astrometry')),
							el('span', { className: 'framesTotal' }, formatExposureTime(attributes.framesSeconds*attributes.framesCount))
						)
					),
                    attributes.equipment != "" && el('label', {}, __('Equipment', 'astrometry') ),
                    attributes.equipment != "" && el(RichText.Content, {
                        tagName: 'p',
                        className : 'equipment',
                        value: attributes.equipment
                    }),

                    attributes.showAstrometryMetaData && el('label', {}, "RA" ),
                    attributes.showAstrometryMetaData && el('p', {}, "{RA}"),
                    attributes.showAstrometryMetaData && el('label', {}, "DEC" ),
                    attributes.showAstrometryMetaData && el('p', {}, "{DEC}"),
                    attributes.showAstrometryMetaData && el('label', {}, __('Job', 'astrometry') ),
                    attributes.showAstrometryMetaData && el('p', {}, "{JOB}"),
                    attributes.showAstrometryMetaData && el('label', {}, __('Objects', 'astrometry') ),
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