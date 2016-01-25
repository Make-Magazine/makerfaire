<!-- Faire entry modals for "list-body.php" -->
<!-- Modal to cancel entry -->
<div class="modal" id="cancelEntry">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Cancel <span id="projName"></span>, Exhibit ID: <span id="cancelEntryID" name="entryID"></span></h4>
      </div>
      <div class="modal-body">
        <div id="cancelText">
          <p>Sorry you can't make it. Why are you canceling?</p><br/>
          <textarea rows="4" cols="50" name="cancelReason"></textarea>
        </div>
        <span id="cancelResponse"></span><br/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="submitCancel">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal to copy entry to a new form -->
<div class="modal" id="copy_entry">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Copy Exhibit ID: <span id="copyEntryID" name="entryID"></span></h4>
      </div>
      <div class="modal-body">
        <?php if(!empty($formArr)) { ?>
          <p>Please choose from the options below:</p><br/>
          <select id="copy2Form">
            <?php foreach($formArr as $availForm) {
              echo '<option value='.$availForm[0].'>'.$availForm[1].'</option>';
            } ?>
          </select>
        <?php } else {
          echo 'No Open faires at the moment';
        } ?>
        <br/><span id="copyResponse"></span><br/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="submitCopy">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!--Modal to delete entry-->
<div class="modal" id="deleteEntry">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Delete <span id="delProjName"></span>, Exhibit ID: <span id="deleteEntryID" name="entryID"></span></h4>
      </div>
      <div class="modal-body">
        <div id="deleteText">
          <p>Are you sure you want to trash this entry? You can not reverse this action.</p>
        </div>
        <span id="deleteResponse"></span><br/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="submitDelete">Yes, delete it</button>
        <button type="button" class="btn btn-default" id="cancelDelete" data-dismiss="modal">No, I'll keep it</button>
        <button type="button" class="btn btn-default" id="closeDelete" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
