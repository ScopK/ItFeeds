package com.scop.org.fydeph.data;

public class Filter {
	public static final int ALL=0;
	public static final int FEED=1;
	public static final int FOLDER=2;
	public static final int TAG=3;
	
	private boolean unread,favorite;
	private String token, unlockToken;
	private String feed,tag,folder;
	private int mode;
	private boolean newFirst;
	
	public Filter(String token){
		this.mode = ALL;
		this.unread = true;
		this.favorite = false;
		this.token = token;
		this.newFirst = true;
		this.unlockToken = "";
	}
	public void viewFolder(String id){
		this.mode = FOLDER;
		this.folder = id;
	}
	public void viewFeed(String id){
		this.mode = FEED;
		this.feed = id;
	}
	public void viewTag(String id){
		this.mode = TAG;
		this.tag = id;
	}
	public void viewAll(){
		this.mode = ALL;
	}
	public void viewNewersFirst(boolean val){
		this.newFirst = val;
	}
	public void viewUnlocked(String st){
		this.unlockToken = st;
	}
	public void viewUnread(boolean u){
		this.unread = u;
	}
	public void viewFavorites(boolean f){
		this.favorite = f;
	}
	public String getQuery(String nextValue){
		String q = "posts?token="+this.token;
		switch (this.mode){
			case FOLDER:	q+="&folder="+this.folder; break;
			case FEED: 		q+="&feed="+this.feed; break;
			case TAG: 		q+="&tag="+this.tag; break;
		}
		if (nextValue.length()>0)		q += "&nextid="+nextValue;
		if (this.unlockToken.length()>0)q += "&lock="+this.unlockToken;
		if (!this.newFirst)	q+="&sortBy=0";
		if (!this.unread)	q+="&unread=0";
		if (this.favorite)	q+="&fav=1";

		return q;
	}
	public boolean isUnread() {
		return unread;
	}
	public boolean isFavorite() {
		return favorite;
	}
	public boolean isNewFirst() {
		return newFirst;
	}
    public int getMode(){
        return this.mode;
    }
    public String getFeed() {
        return feed;
    }
    public String getTag() {
        return tag;
    }
    public String getFolder() {
        return folder;
    }
}
