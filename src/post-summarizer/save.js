import { RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { summaryText } = attributes;
	return (
		<div className="post-summarizer-summary">
			<h3>Summary</h3>
			<RichText.Content tagName="p" value={ summaryText } />
		</div>
	);
}
