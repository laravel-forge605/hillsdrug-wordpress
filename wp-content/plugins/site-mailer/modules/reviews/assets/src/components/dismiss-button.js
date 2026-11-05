import Box from '@elementor/ui/Box';
import Button from '@elementor/ui/Button';
import CloseButton from '@elementor/ui/CloseButton';
import { useStorage } from '@site-mailer/globals';
import { date } from '@wordpress/date';
import { __ } from '@wordpress/i18n';
import { useSettings } from '../hooks/use-settings';

const DimissButton = ( { variant = 'icon' } ) => {
	const { save, get } = useStorage();
	const { currentPage, setIsOpened, handleClose, handleSubmit } = useSettings();
	const handleDismiss = async () => {
		if ( get.hasFinishedResolution ) {
			await save( {
				site_mailer_review_data: {
					...get.data.site_mailer_review_data,
					dismissals: get.data.site_mailer_review_data.dismissals + 1,
					hide_for_days: get.data.site_mailer_review_data.hide_for_days + 30,
					last_dismiss: date( 'Y-m-d H:i:s' ),
				},
			} );
		}

		setIsOpened( false );
	};

	if ( 'icon' === variant ) {
		return <CloseButton onClick={ handleDismiss } />;
	}

	if ( 'button' === variant ) {
		return (
			<Box
				display="flex"
				flexDirection="row"
				gap={ 1 }
				p={ currentPage === 'feedback' ? 2 : 0 }
				pt={ currentPage === 'feedback' ? 0 : 'inherit' }
				width="100%"
				justifyContent="end"
			>
				<Button
					color="secondary"
					variant="text"
					fullWidth={ currentPage === 'feedback' ? false : true }
					sx={ { p: currentPage === 'feedback' ? 0.5 : 2 } }
					onClick={ handleDismiss }
					size="small"
				>
					{ __( 'Not now', 'site-mailer' ) }
				</Button>
				{ currentPage === 'feedback' && (
					<Button
						color="secondary"
						variant="contained"
						onClick={ () => handleSubmit( handleClose ) }
						size="small"
					>
						{ __( 'Submit', 'site-mailer' ) }
					</Button>
				) }
			</Box>
		);
	}
};

export default DimissButton;
