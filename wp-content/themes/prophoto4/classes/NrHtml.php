<?php

class NrHtml {

	public static function group( $elements ) {
		if ( !is_array( $elements ) ) {
			trigger_error( 'NrHtml::group() requires array input', E_USER_NOTICE );
			return '';
		}

		$html = '';
		foreach ( $elements as $method => $args ) {
			if ( $method[0] === '<' ) {
				$html .= $method;
				continue;
			}
			if ( preg_match( '/\_[0-9]$/', $method ) ) {
				$method = preg_replace( '/\_[0-9]$/', '', $method );
			}
			if ( $method === 'group' ) {
				$html .= self::group( $args );
				continue;
			}
			if ( is_int( $method ) ) {
				$method = $args;
				$args = array();
			}
			$arg1 = $arg2 = $arg3 = $arg4 = $arg5 = null;
			if ( !is_array( $args ) ) {
				$arg1 = $args;
			} else {
				@list( $arg1, $arg2, $arg3, $arg4, $arg5 ) = $args;
			}
			if ( method_exists( 'NrHtml', $method ) ) {
				$html .= self::$method( $arg1, $arg2, $arg3, $arg4, $arg5 );
			} else {
				$html .= $method;
			}
		}
		return $html;
	}

	public static function openForm( $action = '', $attr = null, $method = 'post' ) {
		$attr = self::parseAttr( $attr );
		$method = strtolower( $method );
		if ( $method !== 'post' && $method !== 'get' ) {
			$method = 'post';
		}
		return "<form{$attr} action=\"$action\" method=\"$method\" accept-charset=\"utf-8\">";
	}

	public static function closeForm() {
		return '</form>';
	}

	public static function textarea( $name, $value, $rows = null, $cols = null ) {
		$attr = self::parseAttr( array(
			'name' => $name,
			'rows' => $rows,
			'cols' => $cols,
		) );
		return '<textarea' . $attr . '>' . $value . '</textarea>';
	}

	public static function labledTextInput( $label, $name, $value = null, $size = null ) {
		$input = self::textInput( $name, $value, $size );
		return "<label for=\"$name\" class=\"text-input-label " . self::sanitizeAttr( $name ) . "-text-input-label\">$label</label>$input";
	}

	public static function textInput( $name, $value = '', $size = null, $extraAttr = null ) {
		return self::input( 'text', $name, $value, $size, null, $extraAttr );
	}

	public static function hiddenInput( $name, $value = null, $size = null ) {
		return self::input( 'hidden', $name, $value, $size );
	}

	public static function searchInput( $name, $placeholder = null, $size = null ) {
		return self::input( 'search', $name, null, $size, null, $placeholder ? array( 'placeholder' => $placeholder ) : null );
	}

	public static function submit( $value, $attr = null ) {
		return self::input( 'submit', null, $value, null, null, $attr );
	}

	public static function label( $text, $for = null ) {
		return "<label for=\"$for\" class=\"label-for-" . self::sanitizeAttr( $for ) . "\">$text</label>";
	}

	public static function labledCheckbox( $label, $name, $checked = false, $value = 1 ) {
		$checkbox = self::checkbox( $name, $checked, $value );
		return "<label for=\"$name\" class=\"checkbox label-for-checkbox-" . self::sanitizeAttr( $name ) . "\">$checkbox $label</label>";
	}

	public static function checkbox( $name, $checked = false, $value = 1 ) {
		return self::input( 'checkbox', $name, $value, $size = null, $checked );
	}

	private static function input( $type, $name, $value, $size = null, $checked = null, $extraAttr = null ) {
		$attr = array(
			'class'   => 'nr-' . $type . ' ' . $type . '-input-for-' . self::sanitizeAttr( $name ),
			'type'    => $type,
			'name'    => $name,
			'value'   => $value,
			'size'    => $size,
			'checked' => $checked,
		);
		if ( is_array( $extraAttr ) ) {
			$attr = array_merge( $attr, $extraAttr );
		} else if ( is_string( $extraAttr ) ) {
			parse_str( $extraAttr, $extraAttrArray );
			$attr = array_merge( $attr, $extraAttrArray );
		}
		$attrString = self::parseAttr( $attr );
		return "<input{$attrString} />";
	}

	public static function meta() {
		$args = func_get_args();
		$numPairs = func_num_args() / 2;
		if ( !is_int( $numPairs ) || $numPairs === 0 ) {
			return '';
		}
		$meta = "\n\t<meta";
		for ( $i = 0; $i <= $numPairs; $i += 2 ) {
			if ( !isset( $args[$i] ) || !isset( $args[$i+1] ) ) {
				break;
			}
			$meta .= ' ' . $args[$i] . '="' . htmlspecialchars( $args[$i+1], ENT_COMPAT, 'UTF-8', false ) . '"';
		}
		return $meta .= ' />';
	}

	public static function h1( $text, $attr = null ) {
		return self::h( '1', $text, $attr );
	}

	public static function h2( $text, $attr = null ) {
		return self::h( '2', $text, $attr );
	}

	public static function h3( $text, $attr = null ) {
		return self::h( '3', $text, $attr );
	}

	public static function h4( $text, $attr = null ) {
		return self::h( '4', $text, $attr );
	}

	public static function h( $number, $text, $attr = null ) {
		$attr = self::parseAttr( $attr );
		return "<h{$number}{$attr}>$text</h{$number}>";
	}

	public static function p( $text, $attr = null ) {
		return self::tag( 'p', $text, $attr );
	}

	public static function div( $text, $attr = null ) {
		return self::tag( 'div', $text, $attr );
	}

	public static function section( $text, $attr = null ) {
		return self::tag( 'section', $text, $attr );
	}

	public static function span( $text, $attr = null ) {
		return self::tag( 'span', $text, $attr );
	}

	public static function ul( $text, $attr = null ) {
		return self::tag( 'ul', $text, $attr );
	}

	public static function li( $text, $attr = null ) {
		return self::tag( 'li', $text, $attr );
	}

	public static function title( $text ) {
		return "\n\t" . self::tag( 'title', $text );
	}

	public static function tag( $tag, $text, $attr = null ) {
		$attr = self::parseAttr( $attr );
		return "<$tag{$attr}>$text</$tag>";
	}

	public static function button( $text, $attr = null ) {
		return self::tag( 'button', $text, $attr );
	}

	public static function a( $href, $text, $attr = null ) {
		$attr = self::parseAttr( $attr );
		return "<a href=\"$href\"{$attr}>$text</a>";
	}

	public static function img( $src, $attr = null ) {
		$attr = self::parseAttr( $attr );
		return "<img src=\"$src\"{$attr} />";
	}

	public static function link( $rel, $href, $attr = null ) {
		$attr = self::parseAttr( $attr );
		return "\n\t<link rel=\"$rel\" href=\"$href\" {$attr}/>";
	}

	public static function select( $name, $options, $selectedVal = null, $attr = null ) {
		$attr = self::parseAttr( $attr );
		$select = "<select name=\"$name\"{$attr}>";
		foreach ( $options as $optionName => $optionVal ) {
			if ( $selectedVal != null && $selectedVal == $optionVal ) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$select .= "<option value=\"{$optionVal}\"{$selected}>{$optionName}</option>";
		}
		$select .= '</select>';
		return $select;
	}

	public static function radio( $name, $options, $checkedVal = null ) {
		$radio = '';
		foreach ( $options as $label => $val ) {
			$input = self::input( 'radio', $name, $val, null, ( $checkedVal === $val) );
			$radio .= self::div( "$input<label>$label</label>", 'class=radio-btn-wrap' );
		}
		return self::div( $radio, 'class=radio-btns-wrap ' . self::sanitizeAttr( $name ) . '-radio-btns-wrap' );
	}

	private static function sanitizeAttr( $attr ) {
		return strtolower( preg_replace( '/[^a-zA-Z0-9_-]/', '', $attr ) );
	}

	private static function parseAttr( $attr ) {
		if ( !$attr ) {
			return '';
		}
		$attrString = '';

		if ( is_string( $attr ) ) {
			// attrs like "title=Lucy &amp; Rick" mess up parse_str()
			$attr = str_replace( array( '&amp;', '&#', '&quot;' ), array( '~amp~', '~amphash~', '~quot~' ), $attr );
			parse_str( $attr );
		} else if ( is_array( $attr ) ) {
			extract( $attr );
		} else {
			return $attrString;
		}

		if ( isset( $type ) ) {
			$attrString .= ' type="' . $type . '"';
		}
		if ( isset( $name ) ) {
			$attrString .= ' name="' . $name . '"';
		}
		if ( isset( $value ) ) {
			$attrString .= ' value="' . $value . '"';
		}
		if ( isset( $id ) ) {
			$attrString .= ' id="' . $id . '"';
		}
		if ( isset( $class ) ) {
			$attrString .= ' class="' . $class . '"';
		}
		if ( isset( $title ) ) {
			$attrString .= ' title="' . stripslashes( $title ) . '"';
		}
		if ( isset( $size ) ) {
			$attrString .= ' size="' . $size . '"';
		}
		if ( isset( $rows ) ) {
			$attrString .= ' rows="' . $rows . '"';
		}
		if ( isset( $cols ) ) {
			$attrString .= ' cols="' . $cols . '"';
		}
		if ( isset( $rel ) ) {
			$attrString .= ' rel="' . $rel . '"';
		}
		if ( isset( $style ) ) {
			$attrString .= ' style="' . $style . '"';
		}
		if ( isset( $placeholder ) ) {
			$attrString .= ' placeholder="' . $placeholder . '"';
		}
		if ( isset( $checked ) && $checked ) {
			$attrString .= ' checked="checked"';
		}
		if ( isset( $target ) && !empty( $target ) && $target != '_self' ) {
			$attrString .= ' target="' . $target . '"';
		}
		if ( isset( $data ) && is_array( $pieces = explode( '|', $data ) ) ) {
			list( $which, $content ) = $pieces;
			$attrString .= " data-{$which}=\"$content\"";
		}

		return str_replace( array( '~amp~', '~amphash~', '~quot~' ), array( '&amp;', '&#', '&quot;' ), $attrString );
	}

	public static function googleJQuery() {
		$src = ( IS_DEV )
			? 'http://' . $_SERVER['SERVER_NAME'] . '/common/js/jquery.js'
			: 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js';
		return self::scriptSrc( $src );
	}

	public static function scriptSrc( $src ) {
		return "\n\t<script src=\"$src\"></script>";
	}

	public static function stylesheet( $href ) {
		return self::link( 'stylesheet', $href );
	}

	public static function script( $js ) {
		return "\n\t<script>$js</script>";
	}

	public static function style( $css ) {
		return "\n\t<style type=\"text/css\" media=\"screen\">$css</style>";
	}

	public static function cdata( $str ) {
		return "\n\t\t/* <![CDATA[ */\n\t\t\t$str\n\t\t/* ]]> */\n\t";
	}

	public static function lessThanIE( $ver, $str ) {
		return "\n\t<!--[if lt IE $ver]>\n\t\t" . trim( $str ) . "\n\t<![endif]-->";
	}

	public static function jsDeleteCookie( $cookieName ) {
		return self::script( "try{jQuery.cookie('$cookieName','',{expires:-1,path:'/'});} catch(e){}" );
	}

}
