<?php
/**
 * SVG Icons function
 */
if ( ! function_exists('tourfic_get_svg') ) {
	function tourfic_get_svg ( $icon = null ){

		if ( ! $icon ) {
			return;
		}

		switch ( $icon ) {
			case 'heart':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/></svg>';
				break;
			case 'question':
				$output ='<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M11.12 11C11.4335 10.1089 12.0522 9.35748 12.8666 8.87885C13.681 8.40022 14.6385 8.22526 15.5696 8.38495C16.5006 8.54465 17.3451 9.0287 17.9535 9.75138C18.5618 10.474 18.8948 11.3887 18.8934 12.3333C18.8934 15 14.8934 16.3333 14.8934 16.3333M15 21.6667H15.0134M28.3334 15C28.3334 22.3638 22.3638 28.3333 15 28.3333C7.63622 28.3333 1.66669 22.3638 1.66669 15C1.66669 7.63621 7.63622 1.66667 15 1.66667C22.3638 1.66667 28.3334 7.63621 28.3334 15Z" stroke="#002C66" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';
				break;

			case 'share':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>';
				break;

			case 'facebook':
				$output ='<svg height="24" width="24" viewBox="0 0 32 32" role="presentation" aria-hidden="true" focusable="false"><path fill="#3B5998" d="M31 28c0 1.7-1.3 3-3 3H4c-1.7 0-3-1.3-3-3V4c0-1.7 1.3-3 3-3h24c1.7 0 3 1.3 3 3v24z"></path><path fill="#003580" d="M20.4 14V9.7c0-1 .9-.8.9-.8h2.5V5h-4c-4.2 0-4.4 3.7-4.4 3.7V14H13v4.1h2.4V29h4.9V18.1h3.1L24 14h-3.6z"></path><path fill="#FFF" d="M20.4 13V8.7c0-1 .9-.8.9-.8h2.5V4h-4c-4.2 0-4.4 3.7-4.4 3.7V13H13v4.1h2.4V28h4.9V17.1h3.1L24 13h-3.6z"></path></svg>';
				break;

			case 'twitter':
				$output ='<svg height="24" width="24" viewBox="0 0 32 32" role="presentation" aria-hidden="true" focusable="false"><path d="m31 28c0 1.7-1.3 3-3 3h-24c-1.7 0-3-1.3-3-3v-24c0-1.7 1.3-3 3-3h24c1.7 0 3 1.3 3 3z" fill="#00aced"></path><path d="m26.5 9.5c-.8.3-1.6.6-2.4.7.9-.5 1.6-1.4 1.9-2.4-.8.5-1.7.8-2.7 1-.9-.8-2-1.3-3.3-1.3-2.3 0-4.3 1.9-4.3 4.3 0 .3 0 .7.1 1-3.6-.2-6.8-1.9-8.9-4.5-.4.6-.6 1.4-.6 2.1 0 1.5.7 2.8 1.9 3.6-.7 0-1.4-.2-1.9-.5v.1c0 2.1 1.5 3.8 3.4 4.2-.3.2-.6.2-1.1.2-.3 0-.5 0-.8-.1.5 1.7 2.1 2.9 4.1 3-1.5 1.1-3.3 1.8-5.4 1.8-.3 0-.7 0-1-.1 1.9 1.2 4.2 1.9 6.6 1.9 7.9 0 12.3-6.5 12.3-12.3v-.6c.8-.5 1.5-1.2 2.1-2.1" fill="#fff"></path></svg>';
				break;

			case 'checkin':
				$output ='<svg width="20" height="24" viewBox="0 0 20 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M19 10C19 17 10 23 10 23C10 23 1 17 1 10C1 7.61305 1.94821 5.32387 3.63604 3.63604C5.32387 1.94821 7.61305 1 10 1C12.3869 1 14.6761 1.94821 16.364 3.63604C18.0518 5.32387 19 7.61305 19 10Z" stroke="#7893B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M10 13C11.6569 13 13 11.6569 13 10C13 8.34315 11.6569 7 10 7C8.34315 7 7 8.34315 7 10C7 11.6569 8.34315 13 10 13Z" stroke="#7893B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';
				break;

			case 'images':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M15.96 10.29l-2.75 3.54-1.96-2.36L8.5 15h11l-3.54-4.71zM3 5H1v16c0 1.1.9 2 2 2h16v-2H3V5zm18-4H7c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 16H7V3h14v14z"/></svg>';
				break;

			case 'search':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M17.464 6.56a8.313 8.313 0 1 1-15.302 6.504A8.313 8.313 0 0 1 17.464 6.56zm1.38-.586C16.724.986 10.963-1.339 5.974.781.988 2.9-1.337 8.662.783 13.65c2.12 4.987 7.881 7.312 12.87 5.192 4.987-2.12 7.312-7.881 5.192-12.87zM15.691 16.75l7.029 7.03a.75.75 0 0 0 1.06-1.06l-7.029-7.03a.75.75 0 0 0-1.06 1.06z"></path></svg>';
				break;

			case 'calendar_today':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M22.502 13.5v8.25a.75.75 0 0 1-.75.75h-19.5a.75.75 0 0 1-.75-.75V5.25a.75.75 0 0 1 .75-.75h19.5a.75.75 0 0 1 .75.75v8.25zm1.5 0V5.25A2.25 2.25 0 0 0 21.752 3h-19.5a2.25 2.25 0 0 0-2.25 2.25v16.5A2.25 2.25 0 0 0 2.252 24h19.5a2.25 2.25 0 0 0 2.25-2.25V13.5zm-23.25-3h22.5a.75.75 0 0 0 0-1.5H.752a.75.75 0 0 0 0 1.5zM7.502 6V.75a.75.75 0 0 0-1.5 0V6a.75.75 0 0 0 1.5 0zm10.5 0V.75a.75.75 0 0 0-1.5 0V6a.75.75 0 0 0 1.5 0z"></path></svg>';
				break;

			case 'person':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M16.5 6a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0zM18 6A6 6 0 1 0 6 6a6 6 0 0 0 12 0zM3 23.25a9 9 0 1 1 18 0 .75.75 0 0 0 1.5 0c0-5.799-4.701-10.5-10.5-10.5S1.5 17.451 1.5 23.25a.75.75 0 0 0 1.5 0z"></path></svg>';
				break;

			case 'people_outline':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M16.5 13c-1.2 0-3.07.34-4.5 1-1.43-.67-3.3-1-4.5-1C5.33 13 1 14.08 1 16.25V19h22v-2.75c0-2.17-4.33-3.25-6.5-3.25zm-4 4.5h-10v-1.25c0-.54 2.56-1.75 5-1.75s5 1.21 5 1.75v1.25zm9 0H14v-1.25c0-.46-.2-.86-.52-1.22.88-.3 1.96-.53 3.02-.53 2.44 0 5 1.21 5 1.75v1.25zM7.5 12c1.93 0 3.5-1.57 3.5-3.5S9.43 5 7.5 5 4 6.57 4 8.5 5.57 12 7.5 12zm0-5.5c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 5.5c1.93 0 3.5-1.57 3.5-3.5S18.43 5 16.5 5 13 6.57 13 8.5s1.57 3.5 3.5 3.5zm0-5.5c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/></svg>';
				break;

			case 'list_view':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z"/></svg>';
				break;

			case 'grid_view':
				$output ='<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><g fill-rule="evenodd"><path d="M3 3v8h8V3H3zm6 6H5V5h4v4zm-6 4v8h8v-8H3zm6 6H5v-4h4v4zm4-16v8h8V3h-8zm6 6h-4V5h4v4zm-6 4v8h8v-8h-8zm6 6h-4v-4h4v4z"/></g></svg>';
				break;

			default:
			$output = 'SVG NOT FOUND';
				break;
		}

		return $output;
	}
}

/**
 * SVG Icon display
 */
if ( ! function_exists('tourfic_svg') ) {
	function tourfic_svg( $icon = null ){
		echo tourfic_get_svg( $icon );
	}
}