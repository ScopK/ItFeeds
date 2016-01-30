package com.scop.org.itfeeds.data;

public class Label implements MenuLabel {
	private String label;
	
	public Label (String l){
		this.label = l;
	}

	@Override
	public String getLabel() {
		return this.label;
	}

	@Override
	public String getId() {
		return this.label;
	}
	
	@Override
	public String getTitle() {
		return this.label;
	}
}
