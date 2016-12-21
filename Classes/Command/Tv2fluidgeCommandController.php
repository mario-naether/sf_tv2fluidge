<?php
class Tx_SfTv2fluidge_Command_Tv2fluidgeCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{

    /**
     * MigrateContentHelper
     *
     * @var Tx_SfTv2fluidge_Service_MigrateContentHelper
     * @inject
     */
    protected $migrateContentHelper;

    /**
     * @var Tx_SfTv2fluidge_Service_SharedHelper
     * @inject
     */
    protected $sharedHelper;

    /**
     * @param $uidTvTemplate
     * @param $uidBeLayout
     * @param array $data
     * @param bool $markDeleted
     */
    public function migrateContentCommand($uidTvTemplate, $uidBeLayout, array $data, $markDeleted = false)
    {

        var_dump($uidBeLayout);
        var_dump($data);

        /**
         *  tvtemplate => '1' (1 chars)
        belayout => '2' (1 chars)
        tv_col_1 => 'header' (6 chars)
        be_col_1 => '1' (1 chars)
        tv_col_2 => 'content' (7 chars)
        be_col_2 => '0' (1 chars)
        tv_col_3 => 'aside' (5 chars)
        be_col_3 => '2' (1 chars)
        convertflexformoption => 'merge' (5 chars)
        flexformfieldprefix => 'tx_' (3 chars)
        startAction => 'Do it!' (6 chars)
         */

        $this->sharedHelper->setUnlimitedTimeout();

        #$uidTvTemplate = (int)$formdata['tvtemplate'];
       # $uidBeLayout   = (int)$formdata['belayout'];

        $contentElementsUpdated = 0;
        $pageTemplatesUpdated   = 0;

        if ($uidTvTemplate > 0 && $uidBeLayout > 0)
        {
            $pageUids = $this->sharedHelper->getPageIds();

            foreach ($pageUids as $pageUid)
            {
                if ($this->sharedHelper->getTvPageTemplateUid($pageUid) == $uidTvTemplate)
                {
                    $contentElementsUpdated += $this->migrateContentHelper->migrateContentForPage($formdata, $pageUid);
                    $this->migrateContentHelper->migrateTvFlexformForPage($formdata, $pageUid);
                }

                // Update page template (must be called for every page, since to and next_to must be checked
                $pageTemplatesUpdated += $this->migrateContentHelper->updatePageTemplate($pageUid, $uidTvTemplate, $uidBeLayout);
            }

            if ($markDeleted)
            {
                $this->migrateContentHelper->markTvTemplateDeleted($uidTvTemplate);
            }
        }
    }
}