import { useStorage, eventNames, mixpanelService } from '@site-mailer/globals';
import { useState, createContext, useContext } from '@wordpress/element';
import { escapeHTML } from '@wordpress/escape-html';
import { __ } from '@wordpress/i18n';
import APIReview from '../api';

/**
 * Context Component.
 */
const SettingsContext = createContext( null );

export function useSettings() {
	return useContext( SettingsContext );
}

const SettingsProvider = ( { children } ) => {
	const [ rating, setRating ] = useState( 0 );
	const [ feedback, setFeedback ] = useState( '' );
	const [ currentPage, setCurrentPage ] = useState( 'ratings' );
	const [ isOpened, setIsOpened ] = useState( true );
	const [ isRTL, setIsRTL ] = useState( false );
	const { save, get } = useStorage();

	// Notification
	const [ showNotification, setShowNotification ] = useState( false );
	const [ notificationMessage, setNotificationMessage ] = useState( '' );
	const [ notificationType, setNotificationType ] = useState( '' );

	const errorNotification = ( message ) => {
		setNotificationMessage( message );
		setNotificationType( 'error' );
		setShowNotification( true );
	};

	const successNotification = ( message ) => {
		setNotificationMessage( message );
		setNotificationType( 'success' );
		setShowNotification( true );
	};

	/**
	 * Close the popover.
	 * @param {Object} event
	 * @param {string} reason
	 */
	const handleClose = ( event, reason ) => {
		if ( 'backdropClick' !== reason ) {
			setIsOpened( false );
		}

		mixpanelService.sendEvent( eventNames.review.dismissClicked );
	};

	const handleSubmit = async (
		close,
		avoidClosing = false,
		submittedRating = null,
	) => {
		const ratingToSubmit = submittedRating !== null ? submittedRating : rating;
		try {
			const response = await APIReview.sendFeedback( {
				rating: ratingToSubmit,
				feedback,
			} ).then( async ( res ) => {
				await save( {
					site_mailer_review_data: {
						...get.data.site_mailer_review_data,
						rating: parseInt( ratingToSubmit ),
						feedback: escapeHTML( feedback ),
						submitted: true,
					},
				} );

				return res;
			} );

			if ( ratingToSubmit && ! feedback ) {
				mixpanelService.sendEvent( eventNames.review.starSelected, {
					rating: parseInt( ratingToSubmit ),
				} );
			}

			if ( feedback ) {
				mixpanelService.sendEvent( eventNames.review.feedbackSubmitted, {
					feedback_text: escapeHTML( feedback ),
					rating: parseInt( ratingToSubmit ),
				} );
			}

			if ( ! response?.success && parseInt( ratingToSubmit ) < 4 ) {
				/**
				 * Show success message if the feedback was already submitted.
				 */
				successNotification( __( 'Feedback already submitted', 'site-mailer' ) );
			} else if ( response?.success && parseInt( ratingToSubmit ) < 4 ) {
				successNotification( __( 'Thank you for your feedback!', 'site-mailer' ) );
			}

			if ( ! avoidClosing ) {
				await close();
			}

			return true;
		} catch ( e ) {
			errorNotification( __( 'Failed to submit!', 'site-mailer' ) );
			return false;
		}
	};

	return (
		<SettingsContext.Provider
			value={ {
				rating,
				setRating,
				feedback,
				setFeedback,
				currentPage,
				setCurrentPage,
				showNotification,
				setShowNotification,
				notificationMessage,
				setNotificationMessage,
				notificationType,
				setNotificationType,
				isOpened,
				setIsOpened,
				isRTL,
				setIsRTL,
				handleClose,
				handleSubmit,
				errorNotification,
				successNotification,
			} }
		>
			{ children }
		</SettingsContext.Provider>
	);
};

export default SettingsProvider;
