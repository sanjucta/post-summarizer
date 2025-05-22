/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { TextareaControl, Button } from '@wordpress/components';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

import './editor.scss';

export default function Edit( { name, attributes, setAttributes } ) {
	console.log( 'Edit function called' );
	console.log( 'Block name:', name );
	const { summaryText } = attributes;
	const [ loading, setLoading ] = useState( false );

	const postContent = useSelect( ( select ) =>
		select( 'core/editor' ).getEditedPostContent()
	);

	function generateSummary() {
		setLoading( true );

		//make an API call to the server to generate the summary
		fetch( '/wp-json/vsg/v1/summarize_post', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( {
				text: escapeDoubleQuotes(
					removeBlockContent( postContent, name )
				),
			} ),
		} )
			.then( ( response ) => response.json() )
			.then( ( data ) => {
				setAttributes( { summaryText: data.summary } );
				setLoading( false );
			} )
			.catch( ( error ) => {
				console.error( error );
				setLoading( false );
			} );
	}

	return (
		<div { ...useBlockProps() }>
			<Button
				variant="primary"
				onClick={ generateSummary }
				isBusy={ loading }
				style={ { marginTop: '10px' } }
			>
				{ summaryText
					? __( 'Regenerate Summary', 'ai-summary' )
					: __( 'Summarize Post', 'ai-summary' ) }
			</Button>
			{ summaryText && (
				<TextareaControl
					value={ summaryText }
					onChange={ ( value ) => {
						setAttributes( { summaryText: value } );
					} }
				/>
			) }
		</div>
	);
}

/**
 * Escapes double quotes in a string by replacing them with \"
 * @param {string} str - The input string
 * @return {string} - The string with escaped double quotes
 */
function escapeDoubleQuotes( str ) {
	return str && str.replace( /"/g, '\\"' );
}

/**
 * Removes the content of a specific block from post content.
 * @param {string} postContent - The original post content
 * @param {string} blockName   - The block name to remove (e.g., 'wp:vsg/summarizer')
 * @return {string} - The filtered post content
 */
function removeBlockContent( postContent, blockName ) {
	// Parse blocks from content
	const blocks = wp.blocks.parse( postContent );

	// Filter out the target block
	const filteredBlocks = blocks.filter(
		( block ) => block.name !== blockName
	);

	// Serialize back to HTML
	return wp.blocks.serialize( filteredBlocks );
}
