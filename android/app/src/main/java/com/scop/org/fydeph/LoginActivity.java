package com.scop.org.fydeph;

/**
 * Created by Oscar on 22/11/2015.
 */
import org.json.JSONException;
import org.json.JSONObject;

import com.scop.org.fydeph.conn.APICall;
import com.scop.org.fydeph.conn.APICallback;
import com.scop.org.fydeph.data.Content;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;
import android.widget.Toast;


public class LoginActivity extends Activity implements APICallback {
    private SharedPreferences preferences;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        preferences = android.preference.PreferenceManager.getDefaultSharedPreferences(this);

        EditText pass = (EditText) findViewById(R.id.passfield);
        pass.setOnEditorActionListener(new OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                boolean handled = false;
                if (actionId == EditorInfo.IME_ACTION_DONE) {
                    findViewById(R.id.goLogin).performClick();
                }
                return handled;
            }
        });
        loginSetUp();

        String tokenRead = preferences.getString("tokensaved", null);
        if (tokenRead != null){
            login(tokenRead);
        }
    }

    public void loginSetUp(){
        final APICallback callback=this;
        Button loginButton = (Button) findViewById(R.id.goLogin);
        loginButton.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                EditText user = (EditText) findViewById(R.id.loginfield);
                EditText pass = (EditText) findViewById(R.id.passfield);
                String[] loginfo = {user.getText().toString(), pass.getText().toString()};
                if (loginfo[0].equals("")) {
                    Toast.makeText(getApplicationContext(), getString(R.string.empty_user), Toast.LENGTH_LONG).show();
                    return;
                }
                if (loginfo[1].equals("")) {
                    Toast.makeText(getApplicationContext(), getString(R.string.empty_pass), Toast.LENGTH_LONG).show();
                    return;
                }
                Button button = (Button) findViewById(R.id.goLogin);
                button.setText(R.string.logingin);
                button.setEnabled(false);
                new APICall(callback).execute("login?user=" + loginfo[0] + "&pass=" + loginfo[1]);
            }
        });
    }

    private void login(String token){
        Content.get().setToken(token);
        save("locksaved", null);
        /*String lockRead = preferences.getString("locksaved",null);
        if (lockRead != null){
            Content.get().setLock(lockRead);
        }*/

        Content.get().setShowImages(preferences.getBoolean("enable_images", true));
        Content.get().setShowGif(preferences.getBoolean("enable_gifs", false));
        Content.get().setShowVideo(preferences.getBoolean("enable_videos", false));

        Intent intentApp = new Intent(this, MainActivity.class);
        startActivity(intentApp);
    }

    @Override
    public void APIResponse(JSONObject json, int id, APICall parent) throws JSONException {
        Button button = (Button) findViewById(R.id.goLogin);
        button.setText(R.string.login);
        button.setEnabled(true);

        if (json.has("error")){
            String errormsg = json.getString("error");
            if (errormsg.equals("No connection"))
                Toast.makeText(this.getApplicationContext(), R.string.no_connection, Toast.LENGTH_LONG).show();
            else if (errormsg.equals("Incorrect login"))
                Toast.makeText(this.getApplicationContext(), R.string.unknown_user, Toast.LENGTH_LONG).show();
            else
                Toast.makeText(this.getApplicationContext(), errormsg, Toast.LENGTH_LONG).show();
            return;
        }
        String tokenObtained = json.getString("token");
        save("tokensaved", tokenObtained);

        login(tokenObtained);
    }

    public void save(String s,String token){
        SharedPreferences.Editor editor = preferences.edit();
        editor.putString(s, token);
        editor.commit();
    }
}

