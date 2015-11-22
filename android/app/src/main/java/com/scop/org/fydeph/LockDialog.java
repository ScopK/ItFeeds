package com.scop.org.fydeph;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.os.Bundle;
import android.view.ContextThemeWrapper;
import android.view.KeyEvent;
import android.view.View;
import android.view.WindowManager.LayoutParams;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;

public class LockDialog extends Dialog implements android.view.View.OnClickListener {

	public MainActivity c;
	public Dialog d;
	
	public LockDialog(MainActivity a) {
		super(a);
		this.c = a;
	}

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);		
		
		Button yes, no, remove;
		setContentView(R.layout.dialog_lock);
		yes = (Button) findViewById(R.id.confirmlock_button);
		no = (Button) findViewById(R.id.cancellock_button);
		remove = (Button) findViewById(R.id.removelock_button);

		EditText a = (EditText) findViewById(R.id.lockField);
		a.requestFocus();
	    getWindow().setSoftInputMode(LayoutParams.SOFT_INPUT_STATE_VISIBLE);
		
		yes.setOnClickListener(this);
		no.setOnClickListener(this);
		remove.setOnClickListener(this);
		
		setTitle(R.string.insert_lock);
		
        a.setOnEditorActionListener(new OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                boolean handled = false;
                if (actionId == EditorInfo.IME_ACTION_DONE) {
    				EditText et = (EditText) findViewById(R.id.lockField);
    				if (!et.getText().toString().equals(""))
    					c.setLock(et.getText().toString());
    				else
    					c.removeLock();
    				dismiss();
                }
                return handled;
            }
        });
	}
	
	@Override
	public void onClick(View v) {
		switch (v.getId()) {
			case R.id.confirmlock_button:
				EditText et = (EditText) findViewById(R.id.lockField);
				if (!et.getText().toString().equals("")){
					c.setLock(et.getText().toString());
					break;
				}
			case R.id.removelock_button:
				c.removeLock();
				break;
			case R.id.cancellock_button:
			default:
				break;
		}
		dismiss();
	}
}