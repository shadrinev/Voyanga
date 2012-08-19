<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 18:37
 */
interface ITripElementWorkflow
{
    public function createWorkflowAndLinkItWithItem();
    public function saveCredentialsForItem();
    public function createBookingInfoForItem();
    public function switchToSecondWorkflowStage();
}
