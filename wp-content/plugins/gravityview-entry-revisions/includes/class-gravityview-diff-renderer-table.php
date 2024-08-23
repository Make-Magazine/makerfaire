<?php

class GravityView_Diff_Renderer_Table extends WP_Text_Diff_Renderer_Table {

	protected $_diff_threshold = 0.7;

	/**
	 * @var string Label for the single row
	 */
	protected $_row_label = '';

	/**
	 * @var string
	 */
	protected $_row_id = '';

	/**
	 * @var string When a row is empty, show this as the value
	 */
	protected $_empty_value = '';

	/**
	 * @var bool
	 */
	protected $_show_inputs = true;

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 *
	 * @return string
	 */
	public function deletedLine( $line ) {
		return sprintf( "<th scope='row'>%s</th><td class='diff-deletedline'>%s</td>", $this->_row_label, $this->get_line( $line, 'deleted' ) );
	}

	/**
	 * Generates the cell contents for the diff
	 *
	 * @param string $line HTML of the contents of the diff
	 * @param string $side "added" or "deleted"
	 *
	 * @return string
	 */
	private function get_line( $line, $side = 'added' ) {

		if ( '' === $line && '' !== $this->_empty_value ) {
			$line = $this->_empty_value;
		}

		if ( ! $this->_show_inputs ) {
			return $line;
		}

		$row_id  = $this->_row_id ? esc_attr( $this->_row_id ) : '';

		$checked = checked( 'added', $side, false );

		return "<label><input type='radio' class='radio revision_checkbox' name='rows[{$row_id}]' value='{$side}' {$checked} >$line</label>";
	}

	/**
	 * @ignore
	 *
	 * @param string $line HTML-escape the value.
	 *
	 * @return string
	 */
	public function addedLine( $line ) {
		return sprintf( "<td class='diff-addedline diff-enabled'>%s</td>", $this->get_line( $line, 'added' ) );
	}

}