package com.scopyk.fydeph.view;

import com.scopyk.fydeph.R;
import com.scopyk.fydeph.data.Content;

import android.app.Activity;
import android.app.Dialog;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

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
		//requestWindowFeature(Window.FEATURE_NO_TITLE);
		Button yes, no, remove;
		setContentView(R.layout.dialog_lock);
		yes = (Button) findViewById(R.id.confirmlock_button);
		no = (Button) findViewById(R.id.cancellock_button);
		remove = (Button) findViewById(R.id.removelock_button);
		yes.setOnClickListener(this);
		no.setOnClickListener(this);
		remove.setOnClickListener(this);
	}
	
	@Override
	public void onClick(View v) {
		switch (v.getId()) {
			case R.id.confirmlock_button:
				EditText et = (EditText) findViewById(R.id.lockField);
				c.setLock(et.getText().toString());
				break;
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